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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sylius\Bundle\CoreBundle\SyliusOrderEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\EventDispatcher\GenericEvent;
use Sylius\Bundle\CoreBundle\Model\OrderComment;
use Sylius\Bundle\CoreBundle\Form\Type\OrderCommentType;

class OrderCommentController extends Controller
{
    public function indexAction($order_id)
    {
        $order = $this->get('sylius.repository.order')->findOneById($order_id);

        $comments = array();

        if ($order) {
            $comments = $order->getComments();
        }

        return $this->render('SyliusWebBundle:Backend/OrderComment:index.html.twig', array(
            'comments' => $comments,
            'order_id' => $order_id
        ));
    }

    public function createAction(Request $request, $order_id)
    {
        $orderComment = new OrderComment();
        $form = $this->createForm(new OrderCommentType(), $orderComment);

        if ($request->isMethod('POST')) {
            $order = $this->get('sylius.repository.order')->findOneById($order_id);

            if ($order) {
                $user = $this->get('security.context')->getToken()->getUser();

                if ($user) {
                    $form->handleRequest($request);

                    $orderComment->setUser($user);
                    $orderComment->setOrder($order);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($orderComment);
                    $em->flush();

                    $referer = $this->getRequest()->headers->get('referer');
                    return new RedirectResponse($referer);
                }
            }
        }

        return $this->render('SyliusWebBundle:Backend/OrderComment:create.html.twig', array(
            'form' => $form->createView(),
            'order_id' => $order_id
        ));
    }

    public function editAction(Request $request, $id)
    {
        $orderComment = $this->getDoctrine()->getRepository('Sylius\Bundle\CoreBundle\Model\OrderComment')->find($id);
        if ($orderComment) {
            $form = $this->createForm(new OrderCommentType(), $orderComment);

            if ($request->isMethod('POST')) {
                $form->handleRequest($request);

                $em = $this->getDoctrine()->getManager();
                $em->flush();

                $referer = $request->headers->get('referer');
                return new RedirectResponse($referer);
            }

            return $this->render('SyliusWebBundle:Backend/OrderComment:update.html.twig', array(
                'form' => $form->createView(),
                'comment' => $orderComment
            ));
        }
        return new Response('Такой комментарий не найден!');
    }

    public function deleteAction(Request $request, $id)
    {
        $orderComment = $this->getDoctrine()->getRepository('Sylius\Bundle\CoreBundle\Model\OrderComment')->find($id);

        if($orderComment){
            $em = $this->getDoctrine()->getManager();
            $em->remove($orderComment);
            $em->flush();

            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);
        }
        return new Response('Такой комментарий не найден!');
    }
}
