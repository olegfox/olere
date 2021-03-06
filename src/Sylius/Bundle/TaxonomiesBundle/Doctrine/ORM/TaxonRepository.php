<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxonomiesBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Bundle\TaxonomiesBundle\Model\TaxonomyInterface;
use Sylius\Bundle\TaxonomiesBundle\Repository\TaxonRepositoryInterface;

/**
 * Base taxon repository.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class TaxonRepository extends EntityRepository implements TaxonRepositoryInterface
{
    public function getTaxonsAsList(TaxonomyInterface $taxonomy)
    {
        return $this->getQueryBuilder()
            ->where('o.taxonomy = :taxonomy')
            ->andWhere('o.parent IS NOT NULL')
            ->setParameter('taxonomy', $taxonomy)
            ->orderBy('o.left')
            ->getQuery()
            ->getResult()
        ;
    }

//    function cmp($a, $b)
//    {
//        if ($a['percent'] == $b['percent']) {
//            return 0;
//        }
//        return ($a['percent'] < $b['percent']) ? -1 : 1;
//    }

    public function findOneByCatName($nameCat)
    {
//        $percent = "";
//        $taxons = $this->findAll();
//        $p = array();
//        $i = 0;
//        foreach($taxons as $t){
//            $p[$i]['taxon'] = $t;
//            similar_text ( $t->getName(), $nameCat, $percent);
//            $p[$i]['percent'] =  $percent;
//            $i++;
//        }
//        usort($p, "cmp");
//        return json_encode($p);
        $qb = $this->getQueryBuilder();
        $q = $qb
            ->where(
                $qb->expr()->like('o.name', ':name')
            )
            ->setParameter('name', $nameCat.'%')
            ->setMaxResults(1);
        try {
            return $q->getQuery()->getSingleResult();
        }
        catch(\Doctrine\ORM\NoResultException $e) {
            return false;
        }
    }
}
