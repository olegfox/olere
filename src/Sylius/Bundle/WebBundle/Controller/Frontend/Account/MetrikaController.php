<?php

namespace Sylius\Bundle\WebBundle\Controller\Frontend\Account;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sylius\Bundle\CoreBundle\Model\Metrika;
use Sylius\Bundle\CoreBundle\Model\ReportMetrika;

class MetrikaController extends Controller
{
    private function uf_getLastDir($sUrl)
    {
        $sPath = parse_url($sUrl, PHP_URL_PATH); // parse URL and return only path component
        $aPath = explode('/', trim($sPath, '/')); // remove surrounding "/" and return parts into array
        return end($aPath); // last element of array
    }

    /**
     * Добавление новой записи.
     */
    public function indexAction(Request $request)
    {
        $url = $request->get('url');

//      Если пользователь залогонился и параметр url не пуст
        if (!is_null($url) && is_object($this->getUser())) {

//          Получаем каталог
            $repository_taxon = $this->container->get('sylius.repository.taxon');
            $taxon = $repository_taxon->findOneBy(array('slug' => $this->uf_getLastDir($url)));

//          Если каталог существует
            if (is_object($taxon)) {

//              Добавляем новую метрику
                $metrika = new Metrika();
                $metrika->setTaxon($taxon);
                $metrika->setUser($this->getUser());
                $metrika->setType(Metrika::TYPE_CATALOG);

                $em = $this->getDoctrine()->getManager();
                $em->persist($metrika);
                $em->flush();

            }

        } else {

            return new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);

        }

        return new Response('', Response::HTTP_OK);
    }

    public function reportAction($type)
    {
        $em = $this->getDoctrine()->getManager();
        $reportMetrika = new ReportMetrika(); //Новая запись об отправленном отчете
        $mailSubject = '';
        $sql_register = '';
        $sql_login = '';
        $sql_order_cancel = '';
        $sql_order = '';
        $params = array();

//      Отчет за текущий день
        if ($type == 'day') {
            $reportMetrika->setType(ReportMetrika::TYPE_DAY);
            $mailSubject = 'Отчёт за текущий день';
            $date = new \DateTime();
            $sql_register = '
             WHERE YEAR(u.createdAt) = :year
             AND MONTH(u.createdAt) = :month
             AND DAY(u.createdAt) = :day
            ';
            $sql_login = '
             WHERE YEAR(u.lastLogin) = :year
             AND MONTH(u.lastLogin) = :month
             AND DAY(u.lastLogin) = :day
            ';
            $sql_order_cancel = '
             WHERE YEAR(m.datetime) = :year
             AND MONTH(m.datetime) = :month
             AND DAY(m.datetime) = :day
            ';
            $sql_order = '
             WHERE YEAR(o.completedAt) = :year
             AND MONTH(o.completedAt) = :month
             AND DAY(o.completedAt) = :day
            ';
            $params = array(
                'year' => $date->format('Y'),
                'month' => $date->format('m'),
                'day' => $date->format('d')
            );

//      Отчет за неделю
        } elseif ($type == 'week') {
            $reportMetrika->setType(ReportMetrika::TYPE_WEEK);
            $mailSubject = 'Отчёт за неделю';
            $date = new \DateTime();
            $date->modify('-1 week');
            $sql_register = 'WHERE u.createdAt >= :date';
            $sql_login = 'WHERE u.lastLogin >= :date';
            $sql_order_cancel = 'WHERE m.datetime >= :date';
            $sql_order = 'WHERE o.completedAt >= :date';
            $params = array(
                'date' => $date->format('Y-m-d H:i:s')
            );

//      Отчет за месяц
        } elseif ($type == 'month') {
            $reportMetrika->setType(ReportMetrika::TYPE_MONTH);
            $mailSubject = 'Отчёт за месяц';
            $date = new \DateTime();
            $date->modify('-1 month');
            $sql_register = 'WHERE u.createdAt >= :date';
            $sql_login = 'WHERE u.lastLogin >= :date';
            $sql_order_cancel = 'WHERE m.datetime >= :date';
            $sql_order = 'WHERE o.completedAt >= :date';
            $params = array(
                'date' => $date->format('Y-m-d H:i:s')
            );
        }

//      Получим количество регистраций
        $count_register = $em
            ->createQuery('SELECT count(u.id) FROM Sylius\Bundle\CoreBundle\Model\User u ' . $sql_register)
            ->setParameters($params)
            ->getSingleScalarResult();
//      Получим количество аутентификаций
        $count_login = $em
            ->createQuery('SELECT count(u.id) FROM Sylius\Bundle\CoreBundle\Model\User u ' . $sql_login)
            ->setParameters($params)
            ->getSingleScalarResult();
//      Пользователи, которые не купили
        $users = $em
            ->createQuery('SELECT u FROM Sylius\Bundle\CoreBundle\Model\User u WHERE u IN (SELECT IDENTITY(m.user) FROM Sylius\Bundle\CoreBundle\Model\Metrika m ' . $sql_order_cancel . ' AND m.type = 1 )')
            ->setParameters($params)
            ->getResult();
//      Список оформленных заказов
        $orders = $em
            ->createQuery('SELECT o FROM Sylius\Bundle\CoreBundle\Model\Order o ' . $sql_order)
            ->setParameters($params)
            ->getResult();
//      Группируем заказы по пользователям
        $ordersUser = array();
        foreach ($orders as $o) {
            if (!isset($ordersUser[$o->getUser()->getId()]['user'])) {
                $ordersUser[$o->getUser()->getId()]['user'] = $o->getUser();
            }
            $ordersUser[$o->getUser()->getId()]['orders'][] = $o;
        }

//      Создаем отчет
        $reportString = '';
        $reportString = $reportString . 'На сайте зарегистрировалось ' . $count_register . ' чел.<br>';
        $reportString = $reportString . 'Заходило зарегистрированных пользователей ' . $count_login . ' чел.<br>';
        foreach ($users as $u) {
            $reportString = $reportString . $u->getFirstName() . ' добавил(а) в корзину, но не купил(а)<br>';
        }
        foreach ($ordersUser as $o) {
            $reportString = $reportString . $o['user']->getFirstName() . ' оформил(а) заказ(ы) на <br>';
            foreach ($o['orders'] as $order) {
                $reportString = $reportString . $order->getTotal() / 100 . ' руб. <br>';
            }
        }

        $reportMetrika->setText($reportString);
        $em->persist($reportMetrika);
        $em->flush();

//      Отправляем отчет на почту
        $mailer = $this->get('mailer');
        $message = \Swift_Message::newInstance()
            ->setSubject('Olere')
            ->setFrom(array('order@olere.ru' => "Olere " . $mailSubject))
            ->setTo($this->container->getParameter('sylius.email_register'))
            ->setContentType('text/html')
            ->setBody($reportString);
        $mailer->send($message);

        return new Response($reportString);
    }

}
