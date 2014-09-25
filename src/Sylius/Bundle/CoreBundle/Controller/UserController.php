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

        foreach($users as $key => $user){
            $n = $key+1;
            $sheet->setCellValue('A'.$n, $user->getEmail());
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

}
