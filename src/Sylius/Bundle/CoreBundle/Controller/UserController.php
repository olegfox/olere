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

}
