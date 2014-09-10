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
use Sylius\Bundle\CoreBundle\SyliusOrderEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\EventDispatcher\GenericEvent;

class OrderController extends ResourceController
{
    /**
     * @param Request $request
     * @param integer $id
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function indexByUserAction(Request $request, $id)
    {
        $user = $this->get('sylius.repository.user')->findOneById($id);

        if (!$user) {
            throw new NotFoundHttpException('Requested user does not exist.');
        }

        $paginator = $this
            ->getRepository()
            ->createByUserPaginator($user, $this->config->getSorting());

        $paginator->setCurrentPage($request->get('page', 1), true, true);
        $paginator->setMaxPerPage($this->config->getPaginationMaxPerPage());

        return $this->render('SyliusWebBundle:Backend/Order:indexByUser.html.twig', array(
            'user' => $user,
            'orders' => $paginator
        ));
    }

    /**
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function releaseInventoryAction()
    {
        $order = $this->findOr404($this->getRequest());

        $this->get('event_dispatcher')->dispatch(SyliusOrderEvents::PRE_RELEASE, new GenericEvent($order));

        $this->domainManager->update($order);

        $this->get('event_dispatcher')->dispatch(SyliusOrderEvents::POST_RELEASE, new GenericEvent($order));

        return $this->redirectHandler->redirectToReferer();
    }

    private function getFormFactory()
    {
        return $this->get('form.factory');
    }

    public function changeStateAction(Request $request, $id, $state)
    {
        $order = $this->get('sylius.repository.order')->findOneById($id);
        if ($order) {
            if ($state == 7) {
                if ($reasonCancel = $request->get('reasonCancel')) {
                    $order->setReasonCancel($reasonCancel);
                }
            }
            $order->setState($state);
            $m = $this->get('sylius.manager.order');
            $m->flush();
        }
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }

    public function changeQuantityAction(Request $request, $id)
    {
        $orderItem = $this->get('sylius.repository.orderItem')->findOneById($id);
        $manager = $this->getDoctrine()->getManager();
        $json = array();
        if ($orderItem) {
            $quantity = $request->get('quantity');
            if ($quantity >= 1) {
                $change = $orderItem->getQuantity() - $quantity;
                $variant = $orderItem->getVariant();
                $onHand = $variant->getOnHand() + $change;
                if ($onHand >= 0) {
                    $variant->setOnHand($onHand);
                    $orderItem->setQuantity($quantity);
                    $orderItem->setTotal($orderItem->getUnitPrice() * $quantity);
                    $manager->flush();

                    $order = $orderItem->getOrder();
                    $total = 0;
                    foreach ($order->getItems() as $item) {
                        $total += $item->getTotal();
                    }
                    $orderItem->getOrder()->setItemsTotal($total);
                    $orderItem->getOrder()->setTotal($total);
                    $manager->flush();

                    $json = array(
                        'onHand' => $onHand,
                        'total' => ($orderItem->getTotal() / 100) . ' руб.',
                        'amount' => ($total / 100) . ' руб.'
                    );


                } else {
                    return new Response('Недостаточно товара на складе!');
                }
            }
        }
        return new Response(json_encode($json));
    }

    public function deleteItemAction($id)
    {
        $orderItem = $this->get('sylius.repository.orderItem')->findOneById($id);
        $manager = $this->getDoctrine()->getManager();
        if ($orderItem) {

            $order = $orderItem->getOrder();
            $total = 0;
            $quantity = $orderItem->getQuantity();
            $orderItem->getVariant()->setOnHand($orderItem->getVariant()->getOnHand() + $quantity);
            $manager->remove($orderItem);
            $manager->flush();

            foreach ($order->getItems() as $item) {
                $total += $item->getTotal();
            }
            $orderItem->getOrder()->setItemsTotal($total);
            $orderItem->getOrder()->setTotal($total);
            $manager->flush();
            return new Response(json_encode(array(
                'amount' => $total
            )));
        }

        return new Response('no');
    }

    public function deleteAction(Request $request)
    {
        $id = $request->get('id');
        if ($id) {
            $order = $this->get('sylius.repository.order')->findOneById($id);
            $number = $this->get('sylius.repository.number')->findOneBy(array('order' => $id));
            $m = $this->getDoctrine()->getManager();
            if($number){
                $number->setOrder(null);
                $m->remove($number);
                $m->flush();
            }
            $q = $m->createQuery('delete from Sylius\Bundle\CoreBundle\Model\InventoryUnit i where i.orderItem IN (SELECT o.id FROM Sylius\Bundle\CoreBundle\Model\OrderItem o where o.order = :id)')->setParameter('id', $id);
            $q->execute();
            $q = $m->createQuery('delete from Sylius\Bundle\CoreBundle\Model\OrderItem o where o.order = :id')->setParameter('id', $id);
            $q->execute();
            $q = $m->createQuery('delete from Sylius\Bundle\CoreBundle\Model\Order o where o.id = :id')->setParameter('id', $id);
            $numDeleted = $q->execute();
        }
        return $this->redirectHandler->redirectToReferer();
    }

    public function addItemAction(Request $request, $id)
    {
        $order = $this->get('sylius.repository.order')->findOneById($id);
        $sku = $request->get('sku');
        if ($order && $sku != '') {
            $variant = $this->get('sylius.repository.variant')->findOneBy(array('sku' => $sku));
            if ($variant) {
                if ($variant->getOnHand() > 0) {
                    $itemRepository = $this->get('sylius.repository.orderItem');
                    $manager = $this->getDoctrine()->getManager();
                    $variant->setOnHand($variant->getOnHand() - 1);
                    $manager->flush();

                    $item = $itemRepository->createNew();
                    $item
                        ->setVariant($variant)
                        ->setUnitPrice($variant->getPriceOpt())
                        ->setQuantity(1)
                        ->calculateTotal();
                    $order
                        ->addItem($item);
                    $manager->flush();
                } else {
                    return new Response('Товара с артикулом ' . $sku . ' не осталось на складе.');
                }
            } else {
                return new Response('Товар с артикулом ' . $sku . ' не найден.');
            }
        } else {
            return new Response('Не найден заказ или поле артикула не заполнено.');
        }
        return $this->redirectHandler->redirectToReferer();
    }
}
