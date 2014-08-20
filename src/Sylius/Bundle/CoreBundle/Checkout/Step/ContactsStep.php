<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Checkout\Step;

use Sylius\Bundle\CoreBundle\Checkout\SyliusCheckoutEvents;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Symfony\Component\Form\FormInterface;
use Sylius\Bundle\OrderBundle\SyliusOrderEvents;

/**
 * The addressing step of checkout.
 * User enters the shipping and shipping address.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ContactsStep extends CheckoutStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $order = $this->getCurrentCart();
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::CONTACTS_INITIALIZE, $order);

        $form = $this->createCheckoutContactsForm($order);

        return $this->renderStep($context, $order, $form);
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $request = $this->getRequest();

        $order = $this->getCurrentCart();
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::CONTACTS_INITIALIZE, $order);

        $form = $this->createCheckoutContactsForm($order);

        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::CONTACTS_PRE_COMPLETE, $order);

            $this->getManager()->persist($order);
            $this->getManager()->flush();

            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::CONTACTS_COMPLETE, $order);

            $this->completeOrder($order);

            return $this->complete();
        }

        return $this->renderStep($context, $order, $form);
    }

    protected function renderStep(ProcessContextInterface $context, OrderInterface $order, FormInterface $form)
    {
        return $this->render('SyliusWebBundle:Frontend/Checkout/Step:contacts.html.twig', array(
            'order'   => $order,
            'form'    => $form->createView(),
            'context' => $context
        ));
    }

    protected function createCheckoutContactsForm(OrderInterface $order)
    {
        return $this->createForm('sylius_checkout_contacts', $order);
    }

    /**
     * Mark the order as completed.
     *
     * @param OrderInterface $order
     */
    protected function completeOrder(OrderInterface $order)
    {
        $this->dispatchCheckoutEvent(SyliusOrderEvents::PRE_CREATE, $order);
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::FINALIZE_PRE_COMPLETE, $order);

        if ($this->isUserLoggedIn()) {
            $order->setUser($this->getUser());
        }

        $manager = $this->get('sylius.manager.order');
        $manager->persist($order);
        $manager->flush();
        $this->getCurrentCart();
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::FINALIZE_COMPLETE, $order);
        $this->dispatchCheckoutEvent(SyliusOrderEvents::POST_CREATE, $order);
    }

        /**
         * Is user logged in?
         *
         * @return Boolean
         */
        protected function isUserLoggedIn()
    {
        try {
            return $this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED');
        } catch (AuthenticationCredentialsNotFoundException $e) {
            return false;
        }
    }
}
