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

use Sylius\Bundle\TaxonomiesBundle\Model\Taxon as BaseTaxon;
use Doctrine\Common\Collections\ArrayCollection;

class Taxon extends BaseTaxon implements ImageInterface, TaxonInterface
{
    /**
     * @var \SplFileInfo
     */
    protected $file;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var \SplFileInfo
     */
    protected $file2;

    /**
     * @var string
     */
    protected $path2;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    protected $sale;

    /**
     * @var ArrayCollection
     */
    protected $products;

    private $position;

    protected $metaTitle;

    protected $metaKeywords;

    protected $metaDescription;

    protected $metriks;

    public function __construct()
    {
        parent::__construct();

        $this->createdAt = new \DateTime();
        $this->products = new ArrayCollection();
        $this->metriks = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function hasFile()
    {
        return null !== $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function setFile(\SplFileInfo $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFile2()
    {
        return null !== $this->file2;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile2()
    {
        return $this->file2;
    }

    /**
     * {@inheritdoc}
     */
    public function setFile2(\SplFileInfo $file2)
    {
        $this->file2 = $file2;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPath()
    {
        return null !== $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPath2()
    {
        return null !== $this->path2;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath2()
    {
        return $this->path2;
    }

    /**
     * {@inheritdoc}
     */
    public function setPath2($path2)
    {
        $this->path2 = $path2;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * {@inheritdoc}
     */
    public function setProducts($products)
    {
        $this->products = $products;
    }

    public function getCategory()
    {
        $explode = explode("/", $this->getPermalink());
        return $explode[0];
    }

    public function getSubCategory()
    {
        $explode = explode("/", $this->getPermalink());
        return $explode[1];
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getPosition()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function getSale()
    {
        return $this->sale;
    }

    /**
     * {@inheritdoc}
     */
    public function setSale($sale = null)
    {
        $this->sale = $sale;

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaTitle($metaTitle = null)
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaKeywords($metaKeywords = null)
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaDescription($metaDescription = null)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    public function getMetriks()
    {
        return $this->metriks;
    }
}
