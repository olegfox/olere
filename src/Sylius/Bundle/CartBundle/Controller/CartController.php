<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Controller;

use Sylius\Bundle\CartBundle\Event\CartEvent;
use Sylius\Bundle\CartBundle\Event\FlashEvent;
use Sylius\Bundle\CartBundle\SyliusCartEvents;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Default cart controller.
 * It extends the format agnostic resource controller.
 * Resource controller class provides several actions and methods for creating
 * pages and api for your cart system.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartController extends Controller
{
    /**
     * Displays current cart summary page.
     * The parameters includes the form created from `sylius_cart` type.
     *
     * @return Response
     */
    public function summaryAction()
    {
        $cart = $this->getCurrentCart();
        $form = $this->createForm('sylius_cart', $cart);

        return $this->render($this->config->getTemplate('summary.html'), array(
            'cart' => $cart,
            'form' => $form->createView()
        ));
    }

    /**
     * This action is used to submit the cart summary form.
     * If the form and updated cart are valid, it refreshes
     * the cart data and saves it using the operator.
     *
     * If there are any errors, it displays the cart summary page.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function saveAction(Request $request)
    {
        $cart = $this->getCurrentCart();

        $form = $this->createForm('sylius_cart', $cart);
        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $event = new CartEvent($cart);
            $event->isFresh(true);

            $eventDispatcher = $this->getEventDispatcher();

            $eventDispatcher->dispatch(SyliusCartEvents::CART_CHANGE, new GenericEvent($cart));

            // Update models
            $eventDispatcher->dispatch(SyliusCartEvents::CART_SAVE_INITIALIZE, $event);

            // Write flash message
            $eventDispatcher->dispatch(SyliusCartEvents::CART_SAVE_COMPLETED, new FlashEvent());

            $total = $cart->getTotal();
            $r = $total;
            $total = $total / 100;
            if($r % 100 == 0){
                $total = $total . ".00";
            }
            $total = $total . " руб.";

            $cartJson = array(
                "total" => $total,
                "items" => array()
            );
            foreach($cart->getItems() as $item){
                $total = $item->getTotal();
                $r = $total;
                $total = $total / 100;
                if($r % 100 == 0){
                    $total = $total . ".00";
                }
                $total = $total . " руб.";
                $cartJson["items"][]["total"] = $total;
            }
            return new Response(json_encode($cartJson));
        }
        return $this->render($this->config->getTemplate('summary.html'), array(
            'cart' => $cart,
            'form' => $form->createView()
        ));
    }

    /**
     * Clears the current cart using the operator.
     * By default it redirects to cart summary page.
     *
     * @return Response
     */
    public function clearAction()
    {
        // Обнуление счетчика
//        $em = $this->getDoctrine()->getManager();
//        $user = $this->getUser();
//        $user->setFlagClickCart(0);
//        $em->flush();

        $eventDispatcher = $this->getEventDispatcher();

        // Update models
        $eventDispatcher->dispatch(SyliusCartEvents::CART_CLEAR_INITIALIZE, new CartEvent($this->getCurrentCart()));

        // Write flash message
        $eventDispatcher->dispatch(SyliusCartEvents::CART_CLEAR_COMPLETED, new FlashEvent());

        return $this->redirectToCartSummary();
    }
}
