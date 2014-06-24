<?php

namespace Sylius\Bundle\ProductBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

class Import
{
    protected $image;

    protected $file;

    /**
     * Taxons.
     *
     * @var Collection|TaxonInterface[]
     */
    protected $taxons;

    public function getImage()
    {
        return $this->image;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setImage(array $image)
    {
        $this->image = $image;

        return $this;
    }

    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    public function __construct() {
        $this->taxons = new ArrayCollection();
    }

    public function getTaxons()
    {
        return $this->taxons;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxons($taxons)
    {
        $this->taxons = $taxons;

        return $this;
    }
}

