<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\AddressingBundle\Model\AddressInterface;
use Sylius\Bundle\CartBundle\Model\Cart;
use Sylius\Bundle\PaymentsBundle\Model\PaymentInterface;
use Sylius\Bundle\PromotionsBundle\Model\CouponInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionInterface;
use Sylius\Bundle\OrderBundle\Model\AdjustmentInterface;
use Sylius\Bundle\CoreBundle\Model\User;

/**
 * Order entity.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Order extends Cart implements OrderInterface
{
    protected $email;

    protected $username;

    protected $phone;

    protected $city;

    protected $delivery;

    protected $address;

    protected $transport;

    protected $time;

    protected $date;

    protected $comment;

    protected $user;

    protected $reasonCancel;

    protected $timeCounterOrder;

    protected $status;

    /**
     * Order shipping address.
     *
     * @var AddressInterface
     */
    protected $shippingAddress;

    /**
     * Order billing address.
     *
     * @var AddressInterface
     */
    protected $billingAddress;

    /**
     * Shipments for this order.
     *
     * @var Collection|ShipmentInterface[]
     */
    protected $shipments;

    /**
     * Payment.
     *
     * @var PaymentInterface
     */
    protected $payment;

    /**
     * Currency ISO code.
     *
     * @var string
     */
    protected $currency;

    /**
     * Promotion coupon
     *
     * @var CouponInterface
     */
    protected $promotionCoupon;

    /**
     * Order payment state.
     *
     * @var string
     */
    protected $paymentState = PaymentInterface::STATE_NEW;

    /**
     * Order shipping state.
     * It depends on the status of all order shipments.
     *
     * @var string
     */
    protected $shippingState = OrderShippingStates::CHECKOUT;

    /**
     * Promotions applied
     *
     * @var ArrayCollection
     */
    protected $promotions;

    protected $comments;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->shipments = new ArrayCollection();
        $this->promotions = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingAddress(AddressInterface $address)
    {
        $this->shippingAddress = $address;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingAddress(AddressInterface $address)
    {
        $this->billingAddress = $address;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxTotal()
    {
        $taxTotal = 0;

        foreach ($this->getTaxAdjustments() as $adjustment) {
            $taxTotal += $adjustment->getAmount();
        }

        return $taxTotal;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxAdjustments()
    {
        return $this->adjustments->filter(function (AdjustmentInterface $adjustment) {
            return Order::TAX_ADJUSTMENT === $adjustment->getLabel();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function removeTaxAdjustments()
    {
        foreach ($this->getTaxAdjustments() as $adjustment) {
            $this->removeAdjustment($adjustment);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotionTotal()
    {
        $promotionTotal = 0;

        foreach ($this->getPromotionAdjustments() as $adjustment) {
            $promotionTotal += $adjustment->getAmount();
        }

        return $promotionTotal;
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotionAdjustments()
    {
        return $this->adjustments->filter(function (AdjustmentInterface $adjustment) {
            return Order::PROMOTION_ADJUSTMENT === $adjustment->getLabel();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function removePromotionAdjustments()
    {
        foreach ($this->getPromotionAdjustments() as $adjustment) {
            $this->removeAdjustment($adjustment);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingTotal()
    {
        $shippingTotal = 0;

        foreach ($this->getShippingAdjustments() as $adjustment) {
            $shippingTotal += $adjustment->getAmount();
        }

        return $shippingTotal;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingAdjustments()
    {
        return $this->adjustments->filter(function (AdjustmentInterface $adjustment) {
            return Order::SHIPPING_ADJUSTMENT === $adjustment->getLabel();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function removeShippingAdjustments()
    {
        foreach ($this->getShippingAdjustments() as $adjustment) {
            $this->removeAdjustment($adjustment);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * {@inheritdoc}
     */
    public function setPayment(PaymentInterface $payment)
    {
        $this->payment = $payment;
        $this->paymentState = $payment->getState();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentState()
    {
        return $this->paymentState;
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentState($paymentState)
    {
        $this->paymentState = $paymentState;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getInventoryUnits()
    {
        $units = new ArrayCollection;

        foreach ($this->getItems() as $item) {
            foreach ($item->getInventoryUnits() as $unit) {
                $units->add($unit);
            }
        }

        return $units;
    }

    /**
     * {@inheritdoc}
     */
    public function getInventoryUnitsByVariant(VariantInterface $variant)
    {
        return $this->getInventoryUnits()->filter(function (InventoryUnitInterface $unit) use ($variant) {
            return $variant === $unit->getStockable();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getShipments()
    {
        return $this->shipments;
    }

    /**
     * {@inheritdoc}
     */
    public function hasShipments()
    {
        return !$this->shipments->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function addShipment(ShipmentInterface $shipment)
    {
        if (!$this->hasShipment($shipment)) {
            $shipment->setOrder($this);
            $this->shipments->add($shipment);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeShipment(ShipmentInterface $shipment)
    {
        if ($this->hasShipment($shipment)) {
            $shipment->setOrder(null);
            $this->shipments->removeElement($shipment);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasShipment(ShipmentInterface $shipment)
    {
        return $this->shipments->contains($shipment);
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotionCoupon()
    {
        return $this->promotionCoupon;
    }

    /**
     * {@inheritdoc}
     */
    public function setPromotionCoupon(CouponInterface $coupon = null)
    {
        $this->promotionCoupon = $coupon;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotionSubjectItemTotal()
    {
        return $this->getItemsTotal();
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotionSubjectItemCount()
    {
        return $this->items->count();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingState()
    {
        return $this->shippingState;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingState($state)
    {
        $this->shippingState = $state;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isBackorder()
    {
        foreach ($this->getInventoryUnits() as $unit) {
            if (InventoryUnitInterface::STATE_BACKORDERED === $unit->getInventoryState()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets the last updated shipment of the order
     *
     * @return null|ShipmentInterface
     */
    public function getLastShipment()
    {
        if ($this->shipments->isEmpty()) {
            return null;
        }

        $last = $this->shipments->first();
        foreach ($this->shipments as $shipment) {
            if ($shipment->getUpdatedAt() > $last->getUpdatedAt()) {
                $last = $shipment;
            }
        }

        return $last;
    }

    /**
     * Tells is the invoice of the order can be generated.
     *
     * @return Boolean
     */
    public function isInvoiceAvailable()
    {
        if (null !== $lastShipment = $this->getLastShipment()) {
            return (in_array(
                    $lastShipment->getState(),
                    array(ShipmentInterface::STATE_RETURNED, ShipmentInterface::STATE_SHIPPED))
            );
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPromotion(PromotionInterface $promotion)
    {
        return $this->promotions->contains($promotion);
    }

    /**
     * {@inheritdoc}
     */
    public function addPromotion(PromotionInterface $promotion)
    {
        if (!$this->hasPromotion($promotion)) {
            $this->promotions->add($promotion);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removePromotion(PromotionInterface $promotion)
    {
        if ($this->hasPromotion($promotion)) {
            $this->promotions->removeElement($promotion);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotions()
    {
        return $this->promotions;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setDelivery($delivery)
    {
        $this->delivery = $delivery;

        return $this;
    }

    public function getDelivery()
    {
        return $this->delivery;
    }

    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setTransport($transport)
    {
        $this->transport = $transport;

        return $this;
    }

    public function getTransport()
    {
        return $this->transport;
    }

    public function getTransportName()
    {
        $transportName = '';

        switch($this->transport){
            case 0: {
                $transportName = 'Деловые линии';
            }break;
            case 1: {
                $transportName = 'Байкал сервис';
            }break;
            case 2: {
                $transportName = 'Пони экспресс';
            }break;
            case 3: {
                $transportName = 'Почта России';
            }break;
            default: {
                $transportName = '';
            }
        }

        return $transportName;
    }

    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getStateMessage()
    {
        switch($this->state){
            case OrderInterface::STATE_PENDING:{
                return "Ожидает подтверждения";
            }break;
            case 9:{
                return "Ожидает оплаты";
            }break;
            case OrderInterface::STATE_CANCELLED:{
                return "Отмененный заказ";
            }break;
            case OrderInterface::STATE_SHIPPED:{
                return "Ожидает отгрузки";
            }break;
            case OrderInterface::STATE_CONFIRMED:{
                return "Выполненный заказ";
            }break;
        }

        return '';
    }

    public function getButtonMessage()
    {
        switch($this->state){
            case OrderInterface::STATE_PENDING:{
                return "Подтвердить";
            }break;
            case 9:{
                return "Оплачен";
            }break;
            case OrderInterface::STATE_SHIPPED:{
                return "Отгружен";
            }break;
        }

        return '';
    }

    public function getButtonState()
    {
        switch($this->state){
            case OrderInterface::STATE_PENDING:{
                return 9;
            }break;
            case 9:{
                return OrderInterface::STATE_SHIPPED;
            }break;
            case OrderInterface::STATE_SHIPPED:{
                return OrderInterface::STATE_CONFIRMED;
            }break;
        }

        return '';
    }

    public function setReasonCancel($reasonCancel)
    {
        $this->reasonCancel = $reasonCancel;

        return $this;
    }

    public function getReasonCancel()
    {
        return $this->reasonCancel;
    }

    public function setTimeCounterOrder($timeCounterOrder)
    {
        $this->timeCounterOrder = $timeCounterOrder;

        return $this;
    }

    public function getTimeCounterOrder()
    {
        return $this->timeCounterOrder;
    }

    public function getTimeCounterOrderDay(){
        if(is_object($this->timeCounterOrder)){
            return floor((time() - $this->timeCounterOrder->getTimestamp()) / 86400);
        }
        return 0;
    }

    public function setState($state)
    {
        if($this->state != $state){
            $this->setTimeCounterOrder(new \DateTime());
        }
        $this->state = $state;

        return $this;
    }

    public function getNameCompany(){
        if(is_object($this->user)){
            return $this->user->getNameCompany();
        }
        return '';
    }

    public function addComment(OrderComment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    public function removeComment(OrderComment $comment)
    {
        $this->comments->removeElement($comment);
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
