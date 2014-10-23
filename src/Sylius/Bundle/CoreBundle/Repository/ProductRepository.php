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

    public function createBySalePaginator($filter)
    {
        if (isset($filter['price'])) {
            if ($filter['price'] == 'any') {
                $filter['price'] = 10000000;
            } else {
                if ($filter['price'] != 'desc' && $filter['price'] != 'asc') {
                    $filter['price'] = $filter['price'] * 100;
                }
            }
        }
        $queryBuilder = $this->getCollectionQueryBuilder();
        $params = array();

        foreach ($filter as $key => $f) {
            if ($f != 'any' && $f != 'asc' && $f != 'desc') {
                $params[$key] = $f;
            }
        }

        $queryBuilder
            ->innerJoin('product.variants', 'variant')
            ->andWhere('variant.onHand > 0')
            ->andWhere('variant.flagSale = 1');
        if ($filter['material'] != 'any') {
            $queryBuilder
                ->andWhere('variant.metal LIKE :material');
        }
        if (isset($filter['weight'])) {
            if ($filter['weight'] != 'any') {
                $queryBuilder
                    ->andWhere('variant.weight < :weight');
            }
        }
//        if (isset($filter['depth'])) {
//            if ($filter['depth'] != 'any') {
//                $queryBuilder
//                    ->andWhere('variant.depth < :depth');
//            }
//        }
//        if (isset($filter['box'])) {
//            if ($filter['box'] != 'any') {
//                $queryBuilder
//                    ->andWhere('variant.box LIKE :box');
//            }
//        }
        if (isset($filter['size'])) {
            if ($filter['size'] != 'any') {
                $queryBuilder
                    ->andWhere('variant.size = :size');
            }
        }
        $queryBuilder
            ->andWhere('product.enabled = 0');
        if (isset($filter['price'])) {
            if ($filter['price'] == 'desc' || $filter['price'] == 'asc') {
                $queryBuilder
                    ->orderBy('variant.priceSale', $filter['price']);
            } else {
                $queryBuilder
                    ->andWhere('variant.priceSale < :price');
            }
        }

        $queryBuilder
            ->addGroupBy('variant.sku')
            ->setParameters($params);


        return $this->getPaginator($queryBuilder);
    }

    public function createByTaxonPaginator(TaxonInterface $taxon, $sorting = null, $filter = array(), $type = 0)
    {
        if (isset($filter['price'])) {
            if ($filter['price'] == 'any') {
                $filter['price'] = 10000000;
            } else {
                if ($filter['price'] != 'desc' && $filter['price'] != 'asc') {
                    $filter['price'] = $filter['price'] * 100;
                }
            }
        }
        $queryBuilder = $this->getCollectionQueryBuilder();
        if (isset($sorting["position"])) {
            $taxonomyId = $taxon->getTaxonomy()->getId();
            $params = array(
                'taxon' => $taxon
            );

            foreach ($filter as $key => $f) {
                if ($f != 'any' && $f != 'asc' && $f != 'desc') {
                    $params[$key] = $f;
                }
            }

            $queryBuilder
                ->innerJoin('product.taxons', 'taxon')
                ->innerJoin('product.taxons', 'taxon2')
                ->leftJoin('product.variants', 'variant')
                ->andWhere('taxon = :taxon')
                ->andWhere('variant.onHand > 0');
            if (isset($filter['collection'])) {
                if ($filter['collection'] != 'any') {
                    $queryBuilder
                        ->andWhere('taxon2.slug LIKE :collection');
                }
            }
            if (isset($filter['catalog'])) {
                if ($filter['catalog'] != 'any') {
                    $queryBuilder
                        ->andWhere('taxon2.slug LIKE :catalog');
                }
            }
            if (isset($filter['material'])) {
                if ($filter['material'] != 'any') {
                    $queryBuilder
                        ->andWhere('variant.metal LIKE :material');
                }else{
                    $params['notSilver'] = '%серебро%';
                    $queryBuilder
                        ->andWhere('variant.id NOT IN (SELECT v.id FROM Sylius\Bundle\CoreBundle\Model\Variant v WHERE v.metal LIKE :notSilver)');
                }
            }
            if (isset($filter['weight'])) {
                if ($filter['weight'] != 'any') {
                    $queryBuilder
                        ->andWhere('variant.weight < :weight');
                }
            }
//            if (isset($filter['depth'])) {
//                if ($filter['depth'] != 'any') {
//                    $queryBuilder
//                        ->andWhere('variant.depth < :depth');
//                }
//            }
            if (isset($filter['color'])) {
                if ($filter['color'] != 'any') {
                    $queryBuilder
                        ->leftJoin('product.properties', 'property')
                        ->andWhere('property.value LIKE :color');
                }
            }
            if (isset($filter['box'])) {
                if ($filter['box'] != 'any') {
                    $queryBuilder
                        ->andWhere('variant.box LIKE :box');
                }
            }
            if (isset($filter['size'])) {
                if ($filter['size'] != 'any') {
                    $queryBuilder
                        ->andWhere('variant.size = :size');
                }
            }
            $queryBuilder
                ->andWhere('product.enabled = 0');
            if (isset($filter['price'])) {
                if ($type == 0) {
                    if ($filter['price'] == 'desc' || $filter['price'] == 'asc') {
                        $queryBuilder
                            ->orderBy('variant.price', $filter['price']);
                    } else {
                        $queryBuilder
                            ->andWhere('variant.price < :price');
                    }
                } else {
                    if ($filter['price'] == 'desc' || $filter['price'] == 'asc') {
                        $queryBuilder
                            ->orderBy('variant.priceOpt', $filter['price']);
                    } else {
                        $queryBuilder
                            ->andWhere('variant.priceOpt < :price');
                    }
                }
            }
            if (!isset($filter['price'])) {
                if ($taxonomyId == 8) {
                    $queryBuilder
                        ->orderBy("product.position", $sorting["position"]);
                } else {
                    $queryBuilder
                        ->orderBy("product.position2", $sorting["position"]);
                }
            } else {
                if ($filter['price'] != 'desc' && $filter['price'] != 'asc') {
                    if ($taxonomyId == 8) {
                        $queryBuilder
                            ->orderBy("product.position", $sorting["position"]);
                    } else {
                        $queryBuilder
                            ->orderBy("product.position2", $sorting["position"]);
                    }
                }
            }
            $queryBuilder
                ->addGroupBy('variant.sku')
                ->setParameters($params);

        } elseif (isset($sorting["name"])) {
            $queryBuilder
                ->innerJoin('product.taxons', 'taxon')
                ->andWhere('taxon = :taxon')
                ->andWhere('variant.onHand > 0')
                ->orderBy("product.name", $sorting["name"])
                ->andWhere('product.enabled = 0')
                ->setParameter('taxon', $taxon);
        } elseif (isset($sorting["price"])) {
            $queryBuilder
                ->innerJoin('product.taxons', 'taxon')
                ->leftJoin('product.variants', 'variant')
                ->andWhere('taxon = :taxon')
                ->andWhere('variant.onHand > 0')
                ->orderBy("variant.price", $sorting["price"])
                ->andWhere('product.enabled = 0')
                ->setParameter('taxon', $taxon);
        } else {
            $queryBuilder
                ->innerJoin('product.taxons', 'taxon')
                ->leftJoin('product.variants', 'variant')
                ->andWhere('taxon = :taxon')
                ->andWhere('variant.onHand > 0')
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
                ->andWhere('variant.sku LIKE :sku')
                ->setParameter('sku', '%' . $criteria['sku'] . '%');
        }
        if (!empty($criteria['priceBegin'])) {
            $queryBuilder
                ->andWhere('variant.price >= :priceBegin')
                ->setParameter('priceBegin', $criteria['priceBegin'] * 100);
        }
        if (!empty($criteria['priceEnd'])) {
            $queryBuilder
                ->andWhere('variant.price < :priceEnd')
                ->setParameter('priceEnd', $criteria['priceEnd'] * 100);
        }
        if (!empty($criteria['priceOptBegin'])) {
            $queryBuilder
                ->andWhere('variant.priceOpt >= :priceOptBegin')
                ->setParameter('priceOptBegin', $criteria['priceOptBegin'] * 100);
        }
        if (!empty($criteria['priceOptEnd'])) {
            $queryBuilder
                ->andWhere('variant.priceOpt < :priceOptEnd')
                ->setParameter('priceOptEnd', $criteria['priceOptEnd'] * 100);
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
            foreach ($criteria['taxons'] as $k => $t) {
                $taxons = $this->_em->createQueryBuilder()
                    ->select('t')
                    ->from('Sylius\Bundle\CoreBundle\Model\Taxon', 't')
                    ->where('t.taxonomy = :id')
                    ->andWhere('t.parent IS NOT NULL')
                    ->orderBy("t.id", 'ASC')
                    ->setParameter('id', $k)
                    ->getQuery()
                    ->getResult();
                foreach ($t as $val) {
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
