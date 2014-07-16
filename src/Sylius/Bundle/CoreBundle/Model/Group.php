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

use FOS\UserBundle\Model\Group as BaseGroup;

/**
 * Group model.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Group extends BaseGroup implements GroupInterface
{
    protected $showPrice;
    protected $showOptPrice;

    /**
     * {@inheritdoc}
     */
    public function getShowPrice()
    {
        return $this->showPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function setShowPrice($showPrice)
    {
        $this->showPrice = $showPrice;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShowOptPrice()
    {
        return $this->showOptPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function setShowOptPrice($showOptPrice)
    {
        $this->showOptPrice = $showOptPrice;

        return $this;
    }

    public function __construct()
    {
        $this->roles = array();
    }
}
