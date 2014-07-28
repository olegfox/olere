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

class VariantImage extends Image implements VariantImageInterface
{
    /**
     * The associated variant
     *
     * @var VariantInterface
     */
    protected $variant;

    protected $original;

    /**
     * {@inheritdoc}
     */
    public function getVariant()
    {
        return $this->variant;
    }

    /**
     * {@inheritdoc}
     */
    public function setVariant(VariantInterface $variant = null)
    {
        $this->variant = $variant;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * {@inheritdoc}
     */
    public function setOriginal($original)
    {
        $this->original = $original;

        return $this;
    }
}
