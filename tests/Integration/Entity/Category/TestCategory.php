<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Integration\Entity\Category;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Lucaszz\DoctrineDatabaseBackup\tests\Integration\Entity\Product\TestProduct;

/**
 * @ORM\Entity
 * @ORM\Table(name="category")
 */
class TestCategory
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var TestProduct[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Lucaszz\DoctrineDatabaseBackup\tests\Integration\Entity\Product\TestProduct", cascade={"ALL"}, orphanRemoval=true, fetch="EAGER")
     * @ORM\JoinTable(
     *      name="category_products",
     *      joinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")}
     * )
     */
    protected $products;

    /**
     * @param TestProduct[] $products
     */
    public function __construct(array $products)
    {
        $this->products = new ArrayCollection($products);
    }
}
