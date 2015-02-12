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
use Sylius\Bundle\AddressingBundle\Model\ZoneInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface;
use Sylius\Bundle\TaxationBundle\Model\TaxCategoryInterface;
use Sylius\Bundle\VariableProductBundle\Model\VariableProduct as BaseProduct;

/**
 * Sylius core product entity.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Product extends BaseProduct implements ProductInterface
{
    /*
     * Variant selection methods.
     *
     * 1) Choice - A list of all variants is displayed to user.
     *
     * 2) Match  - Each product option is displayed as select field.
     *             User selects the values and we match them to variant.
     */
    const VARIANT_SELECTION_CHOICE = 'choice';
    const VARIANT_SELECTION_MATCH  = 'match';

    /**
     * Short product description.
     * For lists displaying.
     *
     * @var string
     */
    protected $shortDescription;

    /**
     * Variant selection method.
     *
     * @var string
     */
    protected $variantSelectionMethod;

    /**
     * Taxons.
     *
     * @var Collection|TaxonInterface[]
     */
    protected $taxons;

    /**
     * Tax category.
     *
     * @var TaxCategoryInterface
     */
    protected $taxCategory;

    /**
     * Shipping category.
     *
     * @var ShippingCategoryInterface
     */
    protected $shippingCategory;

    /**
     * Not allowed to ship in this zone.
     *
     * @var ZoneInterface
     */
    protected $restrictedZone;

    protected $catalog;

    protected $collection;

    private $position;

    private $position2;

    private $children;

    private $enabled = 0;

    private $new = 0;

    private $action = 0;

    private $warehouse;

    private $hit;

    private $accesories = 0;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setMasterVariant(new Variant());
        $this->taxons = new ArrayCollection();
        $this->variantSelectionMethod = self::VARIANT_SELECTION_CHOICE;
    }

    /**
     * {@inheritdoc}
     */
    public function getSku()
    {
        if(!is_object($this->getMasterVariant())){
            return 0;
        }

        return $this->getMasterVariant()->getSku();
    }

    /**
     * {@inheritdoc}
     */
    public function setSku($sku)
    {
        $this->getMasterVariant()->setSku($sku);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSkuCode()
    {
        if(!is_object($this->getMasterVariant())){
            return 0;
        }
        return $this->getMasterVariant()->getSkuCode();
    }

    /**
     * {@inheritdoc}
     */
    public function setSkuCode($skuCode)
    {
        $this->getMasterVariant()->setSkuCode($skuCode);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getVariantSelectionMethod()
    {
        return $this->variantSelectionMethod;
    }

    /**
     * {@inheritdoc}
     */
    public function setVariantSelectionMethod($variantSelectionMethod)
    {
        if (!in_array($variantSelectionMethod, array(self::VARIANT_SELECTION_CHOICE, self::VARIANT_SELECTION_MATCH))) {
            throw new \InvalidArgumentException(sprintf('Wrong variant selection method "%s" given.', $variantSelectionMethod));
        }

        $this->variantSelectionMethod = $variantSelectionMethod;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isVariantSelectionMethodChoice()
    {
        return self::VARIANT_SELECTION_CHOICE === $this->variantSelectionMethod;
    }

    /**
     * {@inheritdoc}
     */
    public function getVariantSelectionMethodLabel()
    {
        $labels = self::getVariantSelectionMethodLabels();

        return $labels[$this->variantSelectionMethod];
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxons()
    {
        return $this->taxons;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxons(Collection $taxons)
    {
        $this->taxons = $taxons;

        return $this;
    }


    public function addTaxon($taxons)
    {
        $this->taxons[] = $taxons;

        return $this;
    }

    public function removeTaxon($taxons)
    {
        $this->taxons->removeElement($taxons);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice()
    {

        return $this->getMasterVariant()->getPrice();
    }

    /**
     * {@inheritdoc}
     */
    public function setPrice($price)
    {
        $this->getMasterVariant()->setPrice($price);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceOpt()
    {
        return $this->getMasterVariant()->getPriceOpt();
    }

    /**
     * {@inheritdoc}
     */
    public function setPriceOpt($priceOpt)
    {
        $this->getMasterVariant()->setPriceOpt($priceOpt);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceSale()
    {
        return $this->getMasterVariant()->getPriceSale();
    }

    /**
     * {@inheritdoc}
     */
    public function setPriceSale($priceSale)
    {
        $this->getMasterVariant()->setPriceSale($priceSale);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxCategory()
    {
        return $this->taxCategory;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxCategory(TaxCategoryInterface $category = null)
    {
        $this->taxCategory = $category;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingCategory()
    {
        return $this->shippingCategory;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingCategory(ShippingCategoryInterface $category = null)
    {
        $this->shippingCategory = $category;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRestrictedZone()
    {
        return $this->restrictedZone;
    }

    /**
     * {@inheritdoc}
     */
    public function setRestrictedZone(ZoneInterface $zone = null)
    {
        $this->restrictedZone = $zone;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getImages()
    {
        return $this->getMasterVariant()->getImages();
    }

    /**
     * {@inheritdoc}
     */
    public function getImage()
    {
        return $this->getMasterVariant()->getImages()->first();
    }

    /**
     * {@inheritdoc}
     */
    public static function getVariantSelectionMethodLabels()
    {
        return array(
            self::VARIANT_SELECTION_CHOICE => 'Variant choice',
            self::VARIANT_SELECTION_MATCH  => 'Options matching',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCatalog()
    {
        return $this->catalog;
    }

    /**
     * {@inheritdoc}
     */
    public function setCatalog($catalog)
    {
        $this->catalog = $catalog;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * {@inheritdoc}
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition2()
    {
        return $this->position2;
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition2($position2)
    {
        $this->position2 = $position2;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addChildren(Product $child)
    {
        if (!$this->hasChildren($child)) {
            $this->children[] = $child;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeChildren(Product $child)
    {
        $this->children->removeElement($child);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildren(Product $child)
    {
        return $this->children->contains($child);
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNew()
    {
        return $this->new;
    }

    /**
     * {@inheritdoc}
     */
    public function setNew($new)
    {
        $this->new = $new;

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * {@inheritdoc}
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }

    /**
     * {@inheritdoc}
     */
    public function setWarehouse($warehouse)
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHit()
    {
        return $this->hit;
    }

    /**
     * {@inheritdoc}
     */
    public function setHit($hit)
    {
        $this->hit = $hit;

        return $this;
    }

    public function getAccesories()
    {
        return $this->accesories;
    }

    public function setAccesories($accesories)
    {
        $this->accesories = $accesories;

        return $this;
    }

    private function getTaxon($id, $type = 0){
        $taxons = $this->getTaxons();
        foreach($taxons as $t){
            if(is_object($t->getTaxonomy())){
                if($t->getTaxonomy()->getId() == $id){
                    if($type == 0){
                        return $t->getSlug();
                    }
                    return $t->getName();
                }
            }
        }
        return '';
    }

    public function getCatalogSlug(){
        return $this->getTaxon(8);
    }

    public function getCollectionSlug(){
        return $this->getTaxon(9);
    }

    public function getCatalogName(){
        return $this->getTaxon(8, 1);
    }

    public function getCollectionName(){
        return $this->getTaxon(9, 1);
    }

    public function isSilver(){
        if(mb_stripos($this->getMasterVariant()->getMetal(), "серебро", 0, 'UTF-8') !== FALSE){
            return 1;
        }
        return 0;
    }

    public function isRing(){
        if(mb_stripos($this->getName(), "кольц", 0, 'UTF-8') !== FALSE){
            return 1;
        }
        return 0;
    }

}
