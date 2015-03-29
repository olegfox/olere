<?php

namespace Sylius\Bundle\WebBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BanerController extends Controller
{
    public function indexAction(){
        $repository_baner = $this->getDoctrine()->getRepository('Sylius\Bundle\CoreBundle\Model\Baner');

        $baners = $repository_baner->findBy(array(), array('position' => 'ASC'));

        $params = array(
            'baners' => $baners
        );

        return $this->render('SyliusWebBundle:Frontend/Baner:index.html.twig', $params);
    }
}
