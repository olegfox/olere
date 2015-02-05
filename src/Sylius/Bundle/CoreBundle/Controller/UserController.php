<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends ResourceController
{
    /**
     * Render user filter form.
     */
    public function filterFormAction(Request $request)
    {
        return $this->render('SyliusWebBundle:Backend/User:filterForm.html.twig', array(
            'form' => $this->get('form.factory')->createNamed('criteria', 'sylius_user_filter', $request->query->get('criteria'))->createView()
        ));
    }

    public function exportAction(Request $request){
        $users = $this->container->get('sylius.repository.user')->findAll();

        $objPHPExcel = $this->get('phpexcel')->createPHPExcelObject();
        $sheet = $objPHPExcel->setActiveSheetIndex(0);

        // Заголовки
        $sheet->setCellValue('A1', 'Имя');
        $sheet->setCellValue('B1', 'ИНН');
        $sheet->setCellValue('C1', 'Название компании');
        $sheet->setCellValue('D1', 'Телефон');
        $sheet->setCellValue('E1', 'Город');
        $sheet->setCellValue('F1', 'Организационная форма');
        $sheet->setCellValue('G1', 'Профиль деятельности компании');
        $sheet->setCellValue('H1', 'Кол-во торговых точек');
        $sheet->setCellValue('I1', 'Организация');
        $sheet->setCellValue('J1', 'КПП');
        $sheet->setCellValue('K1', 'Расчетный счет');
        $sheet->setCellValue('L1', 'Банк');
        $sheet->setCellValue('M1', 'Корр. счет');
        $sheet->setCellValue('N1', 'БИК');
        $sheet->setCellValue('O1', 'Адрес');
        $sheet->setCellValue('P1', 'Статус');
        $sheet->setCellValue('Q1', 'Email');

        $row = 1;

        foreach($users as $key => $user){
            $row++;
            $sheet->setCellValue('A'.$row, $user->getFirstName());
            $sheet->setCellValue('B'.$row, $user->getInn());
            $sheet->setCellValue('C'.$row, $user->getNameCompany());
            $sheet->setCellValue('D'.$row, $user->getPhone());
            $sheet->setCellValue('E'.$row, $user->getCity());
            $sheet->setCellValue('F'.$row, $user->getFormCompany());
            $sheet->setCellValue('G'.$row, $user->getProfileCompany());
            $sheet->setCellValue('H'.$row, $user->getCountPoint());
            $sheet->setCellValue('I'.$row, $user->getOrganization());
            $sheet->setCellValue('J'.$row, $user->getKpp());
            $sheet->setCellValue('K'.$row, $user->getCurrentAccount());
            $sheet->setCellValue('L'.$row, $user->getBank());
            $sheet->setCellValue('M'.$row, $user->getCorrespondentAccount());
            $sheet->setCellValue('N'.$row, $user->getBik());
            $sheet->setCellValue('O'.$row, $user->getAddress());
            $sheet->setCellValue('P'.$row, $user->getStatus());
            $sheet->setCellValue('Q'.$row, $user->getEmail());
        }

        $writer = $this->get('phpexcel')->createWriter($objPHPExcel, 'Excel2007');

        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename=export.xlsx');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }

    public function updateAction(Request $request){
        if($request->isMethod('PUT')){
            $id = $request->get('id');
            $user = $this->get('fos_user.user_manager')->findUserBy(array('id' => $id)); // get a user from the datastore
            $user->setPlainPassword($request->get('sylius_user')['plainPassword']);
            $user->setTextPassword($request->get('sylius_user')['plainPassword']);
            $this->get('fos_user.user_manager')->updatePassword($user);
            $this->get('fos_user.user_manager')->updateUser($user, false);

            // make more modifications to the database

            $this->getDoctrine()->getManager()->flush();
        }
        return parent::updateAction($request);
    }

    public function deleteAction(Request $request)
    {
        $id = $request->get('id');
        if ($id) {
            $user = $this->get('sylius.repository.user')->findOneById($id);
            $m = $this->getDoctrine()->getManager();
            if(count($user->getOrders()) <= 0){
                $m->remove($user);
                $m->flush();
            }else{
                $orderNumbers = "";
                foreach($user->getOrders() as $order){
                    $orderNumbers = $orderNumbers.$order->getNumber()."  ";
                }
                return new Response("Нельзя удалить этого пользователя, потому что него есть заказы. Номера заказов: ".$orderNumbers);
            }
        }
        return $this->redirectHandler->redirectToReferer();
    }

    public function scanUsersAction(){
        $em = $this->getDoctrine()->getManager();
        $users = $this->get('sylius.repository.user')->findAll();

        foreach($users as $user){
            if($user->getFlagClickCart() >= 3 && (time() - $user->getDateTimeClickCart()->getTimestamp()) > 60*60){
                $user->setFlagClickCart(0);

                $mailer = $this->get('mailer');
                $message = \Swift_Message::newInstance()
                    ->setSubject('Olere')
                    ->setFrom(array('order@olere.ru' => "Olere (Пользователь не оформивший заказ)"))
                    ->setTo(array('moa@olere.ru', 'info@migura.ru', 'knn@olere.ru'))
//                    ->setTo(array('1991oleg22@gmail.com'))
                    ->setBody($this->renderView('SyliusWebBundle:Email:user.not.order.html.twig', array('user' => $user)), 'text/html');
                $mailer->send($message);

                $em->flush();
            }
        }

        return new Response('', Response::HTTP_OK);
    }

}
