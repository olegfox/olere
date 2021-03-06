<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Cart;

use Sylius\Bundle\CartBundle\Model\CartItemInterface;
use Sylius\Bundle\CartBundle\Resolver\ItemResolverInterface;
use Sylius\Bundle\CartBundle\Resolver\ItemResolvingException;
use Sylius\Bundle\CoreBundle\Model\OrderItem;
use Sylius\Bundle\CoreBundle\Model\Product;
use Sylius\Bundle\InventoryBundle\Checker\AvailabilityCheckerInterface;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Sylius\Bundle\CoreBundle\Checker\RestrictedZoneCheckerInterface;
use Sylius\Bundle\CartBundle\Provider\CartProviderInterface;
use Sylius\Bundle\CoreBundle\Calculator\PriceCalculatorInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Item resolver for cart bundle.
 * Returns proper item objects for cart add and remove actions.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ItemResolver implements ItemResolverInterface
{
    /**
     * Cart provider.
     *
     * @var CartProviderInterface
     */
    protected $cartProvider;

    /**
     * Prica calculator.
     *
     * @var PriceCalculatorInterface
     */
    protected $priceCalculator;

    /**
     * Product repository.
     *
     * @var RepositoryInterface
     */
    protected $productRepository;

    /**
     * Sale repository.
     *
     * @var RepositoryInterface
     */
    protected $saleRepository;

    /**
     * Form factory.
     *
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * Stock availability checker.
     *
     * @var AvailabilityCheckerInterface
     */
    protected $availabilityChecker;

    /**
     * Restricted zone checker.
     *
     * @var RestrictedZoneCheckerInterface
     */
    protected $restrictedZoneChecker;

    protected $securityContext;

    /**
     * Constructor.
     *
     * @param CartProviderInterface          $cartProvider
     * @param PriceCalculatorInterface       $priceCalculator
     * @param RepositoryInterface            $productRepository
     * @param RepositoryInterface            $saleRepository
     * @param FormFactoryInterface           $formFactory
     * @param AvailabilityCheckerInterface   $availabilityChecker
     * @param RestrictedZoneCheckerInterface $restrictedZoneChecker
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(
        CartProviderInterface          $cartProvider,
        PriceCalculatorInterface       $priceCalculator,
        RepositoryInterface            $productRepository,
        RepositoryInterface            $saleRepository,
        FormFactoryInterface           $formFactory,
        AvailabilityCheckerInterface   $availabilityChecker,
        RestrictedZoneCheckerInterface $restrictedZoneChecker,
        SecurityContextInterface $securityContext
    )
    {
        $this->cartProvider = $cartProvider;
        $this->priceCalculator = $priceCalculator;
        $this->productRepository = $productRepository;
        $this->saleRepository = $saleRepository;
        $this->formFactory = $formFactory;
        $this->availabilityChecker = $availabilityChecker;
        $this->restrictedZoneChecker = $restrictedZoneChecker;
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(CartItemInterface $item, $data)
    {
        $id = $this->resolveItemIdentifier($data);
        $taxonId = $data->get('taxon');

        /* @var $product Product */
        if (!$product = $this->productRepository->find($id)) {
            throw new ItemResolvingException('Requested product was not found.');
        }

        // We use forms to easily set the quantity and pick variant but you can do here whatever is required to create the item.
        $form = $this->formFactory->create('sylius_cart_item', null, array('product' => $product));

        $form->submit($data);
        /* @var $item OrderItem */
        $item = $form->getData();

        // If our product has no variants, we simply set the master variant of it.
        if (!$product->hasVariants()) {
            $item->setVariant($product->getMasterVariant());
        }

        $variant = $item->getVariant();

        // If all is ok with form, quantity and other stuff, simply return the item.
        if (!$form->isValid() || null === $variant) {
            throw new ItemResolvingException('Submitted form is invalid.');
        }

        if($variant->getFlagSale() == 1){
            $price = $variant->getPriceSale();
        }else{
            $price = $variant->getPriceOpt();
        }
//        $sale = $this->saleRepository->findOneBy(array('taxonId' => $taxonId));
//        if($sale){
//            if(1 == $sale->getTypePrice() || 0 == $sale->getTypePrice()){
//                $price = $price - $price*$sale->getPercent()/100;
//            }
//        }



//        if($this->securityContext->isGranted('ROLE_USER_OPT')){
            $item->setUnitPrice(
                $price
            );
//        }else{
//            $item->setUnitPrice(
//                $variant->getPrice()
//            );
//        }

        $quantity = $item->getQuantity();
        foreach ($this->cartProvider->getCart()->getItems() as $cartItem) {
            if ($cartItem->equals($item)) {
                $quantity += $cartItem->getQuantity();
                break;
            }
        }

        if (!$this->availabilityChecker->isStockSufficient($variant, $quantity)) {
            throw new ItemResolvingException('Selected item is out of stock.');
        }

        if ($this->restrictedZoneChecker->isRestricted($product)) {
            throw new ItemResolvingException('Selected item is not available in your country.');
        }

        return $item;
    }

    /**
     * Here we resolve the item identifier that is going to be added into the cart.
     *
     * @param mixed $request
     *
     * @return string|integer
     *
     * @throws ItemResolvingException
     */
    public function resolveItemIdentifier($request)
    {
        if (!$request instanceof Request) {
            throw new ItemResolvingException('Invalid request data.');
        }

        if (!$request->isMethod('POST') && !$request->isMethod('PUT')) {
            throw new ItemResolvingException('Invalid request method.');
        }

        /*
         * We're getting here product id via query but you can easily override route
         * pattern and use attributes, which are available through request object.
         */
        if (!$id = $request->get('id')) {
            throw new ItemResolvingException('Error while trying to add item to cart.');
        }

        return $id;
    }
}
