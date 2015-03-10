<?php

namespace Sylius\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sylius\Bundle\CoreBundle\Model\Metrika;

class MetrikaController extends Controller
{
    public function indexAction(Request $request)
    {
//      Получаем все метрики посещения каталов за сегоднешний день
        $em = $this->getDoctrine()->getManager();
        $today = new \DateTime();
        $results = $em
            ->createQuery('
             SELECT m FROM Sylius\Bundle\CoreBundle\Model\Metrika m
             WHERE YEAR(m.datetime) = :year
             AND MONTH(m.datetime) = :month
             AND DAY(m.datetime) = :day
             AND m.type = :type
             ')
            ->setParameters(array(
                'year' => $today->format('Y'),
                'month' => $today->format('m'),
                'day' => $today->format('d'),
                'type' => Metrika::TYPE_CATALOG
            ))
            ->getResult();

//      Массив с метриками для вывода в шаблон
        $metriks = array();

//      Если метрики посещения каталогов есть за текущий день
        if (count($results) > 0) {
            foreach ($results as $result) {
                $flag_user = 0; // Флаг, который показывает, есть ли данный пользователь в массиве
                foreach ($metriks as $metrika) {
                    if ($metrika['user']->getId() == $result->getUser()->getId()) {
                        $flag_user = 1;
                    }
                }
//              Если пользователя нет, то добавляем его в массив
                if ($flag_user == 0) {
                    $metriks[$result->getUser()->getId()] = array(
                        'user' => $result->getUser(), //Пользователь
                        'catalogs' => array(), //Каталоги, которые посетил пользователь
                        'orderCancel' => null, //Положил в корзину, но не оформил
                        'order' => null //Оформил заказ или нет
                    );
//                  Добавляем в массив все каталоги, которые посетил пользователь
                    foreach ($results as $r) {
                        $flag_taxon = 0; // Флаг, который показывает, есть ли данный каталог в массиве
                        foreach ($metriks[$result->getUser()->getId()]['catalogs'] as $catalog){
                            if ($catalog == $r->getTaxon()->getName()) {
                                $flag_taxon = 1;
                            }
                        }
//                      Если такой каталог ещё не добавлен, то добавляем в массив
                        if ($flag_taxon == 0) {
                            $metriks[$result->getUser()->getId()]['catalogs'][] = $r->getTaxon()->getName();
                        }
                    }
//                  Определяем факт того, что пользователь добавил в корзину, но не оформил
                    $not_order = $em
                        ->createQuery('
                         SELECT count(m.id) FROM Sylius\Bundle\CoreBundle\Model\Metrika m
                         WHERE YEAR(m.datetime) = :year
                         AND MONTH(m.datetime) = :month
                         AND DAY(m.datetime) = :day
                         AND m.type = :type
                         ')
                        ->setParameters(array(
                            'year' => $today->format('Y'),
                            'month' => $today->format('m'),
                            'day' => $today->format('d'),
                            'type' => Metrika::TYPE_NOT_ORDER
                        ))
                        ->getSingleScalarResult();
                    if($not_order > 0){
                        $metriks[$result->getUser()->getId()]['orderCancel'] = true;
                    }
//                  Определяем был ли заказ
                    $order = $em
                        ->createQuery('
                             SELECT count(o.id) FROM Sylius\Bundle\CoreBundle\Model\Order o
                             WHERE YEAR(o.completedAt) = :year
                             AND MONTH(o.completedAt) = :month
                             AND DAY(o.completedAt) = :day
                             AND o.user = :user
                             ')
                        ->setParameters(array(
                            'user' => $result->getUser(),
                            'year' => $today->format('Y'),
                            'month' => $today->format('m'),
                            'day' => $today->format('d')
                        ))
                        ->getSingleScalarResult();
                    if($order > 0){
                        $metriks[$result->getUser()->getId()]['order'] = true;
                    }
                }
            }
        }

//      Если идёт запрос активности в excel файл
        if($request->get('type') == 'excel'){
            $objPHPExcel = $this->get('phpexcel')->createPHPExcelObject();
            $sheet = $objPHPExcel->setActiveSheetIndex(0);

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

            // Начальная строчка для вывода в файл
            $row = 4;

            foreach($metriks as $m){
                $sheet->setCellValue('A'.$row, $m['user']->getFirstName());
                $sheet->setCellValue('B'.$row, $m['user']->getPhone());
                $sheet->setCellValue('C'.$row, $m['user']->getNameCompany());
                $sheet->setCellValue('D'.$row, $m['user']->getCity());
                $sheet->setCellValue('E'.$row, $m['user']->getLastLogin()->format('H:i'));
                $sheet->setCellValue('F'.$row, join(', ', $m['catalogs']));
                $sheet->setCellValue('G'.$row, $m['orderCancel'] ? 'Да' : 'Нет');
                $sheet->setCellValue('H'.$row, $m['order'] ? 'Да' : 'Нет');
                $row++;
            }

            $writer = $this->get('phpexcel')->createWriter($objPHPExcel, 'Excel2007');

            $response = $this->get('phpexcel')->createStreamedResponse($writer);
            // adding headers
            $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
            $response->headers->set('Content-Disposition', 'attachment;filename=metrika.xlsx');
            $response->headers->set('Pragma', 'public');
            $response->headers->set('Cache-Control', 'maxage=1');

            return $response;
        }

//      Если нет, то выводим просто шаблон в админ панели с активностью
        $params = array(
            'metriks' => $metriks
        );
        return $this->render('SyliusWebBundle:Backend/Metrika:index.html.twig', $params);
    }
}
