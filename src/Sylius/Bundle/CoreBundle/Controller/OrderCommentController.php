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

namespace Sylius\Bundle\CoreBundle\Controller;
use Sylius\Bundle\CoreBundle\SyliusOrderEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\EventDispatcher\GenericEvent;

class OrderCommentController extends Controller
{
    public function indexAction($order_id){
        $order = $this->get('sylius.repository.order')->findOneById($order_id);

        $comments = array();

        if($order){
            $comments = $order->getComments();
        }

        return $this->render('SyliusWebBundle:Backend/OrderComment:index.html.twig', array(
            'comments' => $comments
        ));
    }

    public function createAction(Request $request, $order_id){

    }

    public function editAction(Request $request, $id){

    }

    public function deleteAction($id){

    }
}
