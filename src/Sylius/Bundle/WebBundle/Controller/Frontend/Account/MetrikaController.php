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

//      Уведомление о зашедшом пользователе за час
        if ($type == 'hour') {
//          Получили метрики пользователей, которые зашли за последний час
            $date = new \DateTime();
            $date->modify('-1 hour');
            $results = $em
                ->createQuery('
             SELECT m FROM Sylius\Bundle\CoreBundle\Model\Metrika m
             JOIN m.user u
             WHERE u.lastLogin > :datetime
             AND m.datetime > :datetime
             AND m.type = :type
             ')
                ->setParameters(array(
                    'datetime' => $date->format('Y-m-d H:i:s'),
                    'type' => Metrika::TYPE_CATALOG
                ))
                ->getResult();

            $metriks = array();

            foreach($results as $r){
                if(!isset($metriks[$r->getUser()->getId()])){
                    $metriks[$r->getUser()->getId()]['user'] = $r->getUser();
                }

                $flag_taxon = 0; // Флаг, который показывает, есть ли данный каталог в массиве
                if (isset($metriks[$r->getUser()->getId()]['catalogs'])) {
                    foreach ($metriks[$r->getUser()->getId()]['catalogs'] as $catalog) {
                        if ($catalog == $r->getTaxon()->getName()) {
                            $flag_taxon = 1;
                        }
                    }
                }

//              Если такой каталог ещё не добавлен, то добавляем в массив
                if ($flag_taxon == 0) {
                    $metriks[$r->getUser()->getId()]['catalogs'][] = $r->getTaxon()->getName();
                }
            }

//          Рассылаем уведомления
            foreach($metriks as $m){
                $mailer = $this->get('mailer');
                $message = \Swift_Message::newInstance()
                    ->setSubject('Olere')
                    ->setFrom(array('order@olere.ru' => "Olere (Новый вошедший пользователь)"))
                    ->setTo($this->container->getParameter('sylius.email_register'))
                    ->setBody($this->renderView('SyliusWebBundle:Email:user.login.html.twig', array('user' => $m['user'], 'catalogs' => join(', ', $m['catalogs']))), 'text/html');
                $mailer->send($message);
            }

            return new Response('', 200);

        } //      Отчет за текущий день
        elseif ($type == 'day') {
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
            ->createQuery('SELECT u FROM Sylius\Bundle\CoreBundle\Model\User u WHERE u IN (SELECT IDENTITY(m.user) FROM Sylius\Bundle\CoreBundle\Model\Metrika m ' . $sql_order_cancel . ' AND m.type = 1 ) ORDER BY u.lastLogin ASC')
            ->setParameters($params)
            ->getResult();
//      Список оформленных заказов
        $orders = $em
            ->createQuery('SELECT o FROM Sylius\Bundle\CoreBundle\Model\Order o LEFT JOIN o.user u ' . $sql_order . ' ORDER BY u.lastLogin ASC')
            ->setParameters($params)
            ->getResult();
//      Все метрики посещения каталогов
        $metriks = $em
            ->createQuery('SELECT m FROM Sylius\Bundle\CoreBundle\Model\Metrika m LEFT JOIN m.user u ' . $sql_order_cancel . ' AND m.type = 0 ORDER BY u.lastLogin ASC')
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

//      Группируем данные по пользователям
        $allData = array();

//      Добавляем информацию о несовершенных покупках
        foreach ($users as $u) {
            if (!isset($allData[$u->getId()])) {
                $allData[$u->getId()]['user'] = $u;
                $allData[$u->getId()]['not_order'] = 1;
            }
        }

//      Добавляем информацию об оформленных заказах
        foreach ($ordersUser as $o) {
            if (!isset($allData[$o['user']->getId()])) {
                $allData[$o['user']->getId()]['user'] = $o['user'];
            }

            $allData[$o['user']->getId()]['orders'] = $o['orders'];
        }

//      Добавляем информацию о посещённых каталогах
        foreach ($metriks as $m) {
            if (!isset($allData[$m->getUser()->getId()])) {
                $allData[$m->getUser()->getId()]['user'] = $m->getUser();
            }

            $flag_taxon = 0; // Флаг, который показывает, есть ли данный каталог в массиве
            if (isset($allData[$m->getUser()->getId()]['catalogs'])) {
                foreach ($allData[$m->getUser()->getId()]['catalogs'] as $catalog) {
                    if ($catalog == $m->getTaxon()->getName()) {
                        $flag_taxon = 1;
                    }
                }
            }

//          Если такой каталог ещё не добавлен, то добавляем в массив
            if ($flag_taxon == 0) {
                $allData[$m->getUser()->getId()]['catalogs'][] = $m->getTaxon()->getName();
            }

        }

        $view = $this->renderView('SyliusWebBundle:Frontend/Metrika:report.html.twig', array('data' => $allData, 'countRegister' => $count_register, 'countLogin' => $count_login));

        $objPHPExcel = $this->get('phpexcel')->createPHPExcelObject();
        $sheet = $objPHPExcel->setActiveSheetIndex(0);

//      Excel отчет для вложения в письмо
        // Дата создания отчета
        $sheet->setCellValue('A1', 'Дата создания отчета');
        $sheet->setCellValue('B1', (new \DateTime())->format('d.m.Y'));

        // Заголовки
        $sheet->setCellValue('A3', 'Имя пользователя');
        $sheet->setCellValue('B3', 'Телефон');
        $sheet->setCellValue('C3', 'Название компании');
        $sheet->setCellValue('D3', 'Город');
        $sheet->setCellValue('E3', 'Когда заходил?');
        $sheet->setCellValue('F3', 'Посетил');
        $sheet->setCellValue('G3', 'Добавил в корзину, но не оформил');
        $sheet->setCellValue('H3', 'Оформил заказ');
        $sheet->setCellValue('I3', 'Суммы заказов');

//      Установка жирного шрифта у заголовков
        $objPHPExcel->getActiveSheet()->getStyle('A3:I3')->getFont()->setBold(true);

        // Начальная строчка для вывода в файл
        $row = 4;

        foreach ($allData as $m) {
            $sheet->setCellValue('A' . $row, $m['user']->getFirstName());
            $sheet->setCellValue('B' . $row, $m['user']->getPhone());
            $sheet->setCellValue('C' . $row, $m['user']->getNameCompany());
            $sheet->setCellValue('D' . $row, $m['user']->getCity());

            if ($type == 'day') {
                $sheet->setCellValue('E' . $row, $m['user']->getLastLogin()->format('H:i'));
            } else {
                $sheet->setCellValue('E' . $row, $m['user']->getLastLogin()->format('d.m.Y H:i'));
            }

            $sheet->setCellValue('F' . $row, join(', ', $m['catalogs']));
            $sheet->setCellValue('G' . $row, isset($m['not_order']) ? 'Да' : 'Нет');
            $sheet->setCellValue('H' . $row, isset($m['orders']) ? 'Да' : 'Нет');

            if (isset($m['orders'])) {
                $totalString = '';
                $i = 0;
                foreach ($m['orders'] as $order) {
                    $totalString = $totalString . $order->getTotal() / 100;
                    $i++;
                    if ($i != count($m['orders'])) {
                        $totalString = $totalString . ', ';
                    }
                }
                $sheet->setCellValue('I' . $row, $totalString);
            }

            $row++;
        }

//      Установка автоширины колонок
        foreach (range('A', 'I') as $columnID) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        $writer = $this->get('phpexcel')->createWriter($objPHPExcel, 'Excel2007');
        $writer->save(getcwd() . '/export/metrika_' . $type . '.xlsx');

//        $reportMetrika->setText($view);
//        $em->persist($reportMetrika);
//        $em->flush();

//      Отправляем отчет на почту
        $mailer = $this->get('mailer');
        $message = \Swift_Message::newInstance()
            ->setSubject('Olere')
            ->setFrom(array('order@olere.ru' => "Olere " . $mailSubject))
            ->setTo($this->container->getParameter('sylius.email_register'))
//            ->setTo('1991oleg22@gmail.com')
            ->setContentType('text/html');

        if ($type == 'day') {
            $message
                ->setBody($view, 'text/html');
        }

        $message
            ->attach(\Swift_Attachment::fromPath(getcwd() . '/export/metrika_' . $type . '.xlsx'));
        $mailer->send($message);

        return new Response($view);
    }

}
