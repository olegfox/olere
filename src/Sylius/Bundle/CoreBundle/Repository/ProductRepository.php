<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Repository;

use Sylius\Bundle\CoreBundle\Model\ProductInterface;
use Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface;
use Sylius\Bundle\VariableProductBundle\Doctrine\ORM\VariableProductRepository;

/**
 * Product repository.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ProductRepository extends VariableProductRepository
{
    /**
     * Create paginator for products categorized
     * under given taxon.
     *
     * @param TaxonInterface $taxon
     *
     * @return PagerfantaInterface
     */
    public function createByTaxonPaginator(TaxonInterface $taxon, $sorting = null, $price = 'any', $type = 0)
    {
        if($price == 'any'){
            $price = 10000000;
        }
        $queryBuilder = $this->getCollectionQueryBuilder();
        if (isset($sorting["position"])) {
            $taxonomyId = $taxon->getTaxonomy()->getId();

            if($taxonomyId == 8){
                if($type == 0){
                    $queryBuilder
                        ->innerJoin('product.taxons', 'taxon')
                        ->leftJoin('product.variants', 'variant')
                        ->andWhere('taxon = :taxon')
                        ->andWhere('variant.price < :price')
                        ->andWhere('product.enabled = 0')
                        ->orderBy("product.position", $sorting["position"])
                        ->setParameters(array(
                            'taxon' => $taxon,
                            'price' => $price*100
                        ));
                }else{
                    $queryBuilder
                        ->innerJoin('product.taxons', 'taxon')
                        ->leftJoin('product.variants', 'variant')
                        ->andWhere('taxon = :taxon')
                        ->andWhere('variant.priceOpt < :price')
                        ->andWhere('product.enabled = 0')
                        ->orderBy("product.position", $sorting["position"])
                        ->setParameters(array(
                            'taxon' => $taxon,
                            'price' => $price*100
                        ));
                }
            }else{
                if($type == 0){
                    $queryBuilder
                        ->innerJoin('product.taxons', 'taxon')
                        ->leftJoin('product.variants', 'variant')
                        ->andWhere('taxon = :taxon')
                        ->andWhere('variant.price < :price')
                        ->andWhere('product.enabled = 0')
                        ->orderBy("product.position2", $sorting["position"])
                        ->setParameters(array(
                            'taxon' => $taxon,
                            'price' => $price*100
                        ));
                }else{
                    $queryBuilder
                        ->innerJoin('product.taxons', 'taxon')
                        ->leftJoin('product.variants', 'variant')
                        ->andWhere('taxon = :taxon')
                        ->andWhere('variant.priceOpt < :price')
                        ->andWhere('product.enabled = 0')
                        ->orderBy("product.position2", $sorting["position"])
                        ->setParameters(array(
                            'taxon' => $taxon,
                            'price' => $price*100
                        ));
                }
            }
        }elseif (isset($sorting["name"])) {
            $queryBuilder
                ->innerJoin('product.taxons', 'taxon')
                ->andWhere('taxon = :taxon')
                ->orderBy("product.name", $sorting["name"])
                ->andWhere('product.enabled = 0')
                ->setParameter('taxon', $taxon);
        }elseif(isset($sorting["price"])){
            $queryBuilder
                ->innerJoin('product.taxons', 'taxon')
                ->leftJoin('product.variants', 'variant')
                ->andWhere('taxon = :taxon')
                ->orderBy("variant.price", $sorting["price"])
                ->andWhere('product.enabled = 0')
                ->setParameter('taxon', $taxon);
        }else{
            $queryBuilder
                ->innerJoin('product.taxons', 'taxon')
                ->andWhere('taxon = :taxon')
                ->andWhere('product.enabled = 0')
                ->setParameter('taxon', $taxon);
        }

        return $this->getPaginator($queryBuilder);
    }

    /**
     * Create filter paginator.
     *
     * @param array $criteria
     * @param array $sorting
     * @param Boolean $deleted
     *
     * @return PagerfantaInterface
     */
    public function createFilterPaginator($criteria = array(), $sorting = array(), $deleted = false)
    {
        $queryBuilder = parent::getCollectionQueryBuilder()
            ->select('product, variant, taxon')
            ->leftJoin('product.variants', 'variant')
            ->join('product.taxons', 'taxon');

        if (!empty($criteria['name'])) {
            $queryBuilder
                ->andWhere('product.name LIKE :name')
                ->setParameter('name', '%' . $criteria['name'] . '%');
        }
        if (!empty($criteria['sku'])) {
            $queryBuilder
                ->andWhere('variant.sku = :sku')
                ->setParameter('sku', $criteria['sku']);
        }
        if (!empty($criteria['priceBegin'])) {
            $queryBuilder
                ->andWhere('variant.price >= :priceBegin')
                ->setParameter('priceBegin', $criteria['priceBegin']*100);
        }
        if (!empty($criteria['priceEnd'])) {
            $queryBuilder
                ->andWhere('variant.price < :priceEnd')
                ->setParameter('priceEnd', $criteria['priceEnd']*100);
        }
        if (!empty($criteria['priceOptBegin'])) {
            $queryBuilder
                ->andWhere('variant.priceOpt >= :priceOptBegin')
                ->setParameter('priceOptBegin', $criteria['priceOptBegin']*100);
        }
        if (!empty($criteria['priceOptEnd'])) {
            $queryBuilder
                ->andWhere('variant.priceOpt < :priceOptEnd')
                ->setParameter('priceOptEnd', $criteria['priceOptEnd']*100);
        }
        if (!empty($criteria['skuCode'])) {
            $queryBuilder
                ->andWhere('variant.skuCode = :skuCode')
                ->setParameter('skuCode', $criteria['skuCode']);
        }
        if (!empty($criteria['enabled'])) {
            $queryBuilder
                ->andWhere('product.enabled = :enabled')
                ->setParameter('enabled', $criteria['enabled']);
        }
        if (!empty($criteria['taxons'])) {
            $id = array();
            foreach($criteria['taxons'] as $k => $t){
                $taxons = $this->_em->createQueryBuilder()
                    ->select('t')
                    ->from('Sylius\Bundle\CoreBundle\Model\Taxon', 't')
                    ->where('t.taxonomy = :id')
                    ->andWhere('t.parent IS NOT NULL')
                    ->orderBy("t.id", 'ASC')
                    ->setParameter('id', $k)
                    ->getQuery()
                    ->getResult();
                foreach($t as $val){
                    $id[] = $taxons[intval($val)]->getId();
                }
            }
            $queryBuilder
                ->andWhere('taxon.id IN (:taxons)')
                ->setParameter('taxons', $id);
        }

        if (empty($sorting)) {
            if (!is_array($sorting)) {
                $sorting = array();
            }
            $sorting['updatedAt'] = 'desc';
        }

        $this->applySorting($queryBuilder, $sorting);

        if ($deleted) {
//            $this->_em->getFilters()->disable('softdeleteable');
        }
//$products = $queryBuilder->getQuery()
//    ->getResult();
//        foreach($products as $p){
//            print $p->getId()." ";
//        }

        return $this->getPaginator($queryBuilder);
    }

    /**
     * Get the product data for the details page.
     *
     * @param integer $id
     *
     * @return null|ProductInterface
     */
    public function findForDetailsPage($id)
    {
        $queryBuilder = $this->getQueryBuilder();

//        $this->_em->getFilters()->disable('softdeleteable');

        $queryBuilder
            ->leftJoin('variant.images', 'image')
            ->addSelect('image')
            ->andWhere($queryBuilder->expr()->eq('product.id', ':id'))
            ->setParameter('id', $id);

        $result = $queryBuilder
            ->getQuery()
            ->getOneOrNullResult();

        return $result;
    }

    /**
     * Find X recently added products.
     *
     * @param integer $limit
     *
     * @return ProductInterface[]
     */
    public function findLatest($limit = 10)
    {
        return $this->findBy(array(), array('createdAt' => 'desc'), $limit);
    }

    public function findForName($name)
    {
        $queryBuilder = $this->getCollectionQueryBuilder();

        $queryBuilder
            ->andWhere($queryBuilder->expr()->like('product.name', ':name'))
            ->setParameter('name', "%" . $name . "%");

        $result = $queryBuilder
            ->getQuery()
            ->getResult();;

        return $result;
    }

    public function findForPrice($sorting)
    {
        $queryBuilder = parent::getCollectionQueryBuilder()
            ->select('product, variant')
            ->leftJoin('product.variants', 'variant')
            ->orderBy("variant.price", $sorting);

        $result = $queryBuilder
            ->getQuery()
            ->getResult();;

        return $result;
    }
}
