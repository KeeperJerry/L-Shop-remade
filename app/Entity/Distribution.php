<?php
declare(strict_types = 1);

namespace app\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="distributions")
 */
class Distribution
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="app\Entity\PurchaseItem")
     */
    private $purchaseItem;

    public function __construct(PurchaseItem $purchaseItem)
    {
        $this->purchaseItem = $purchaseItem;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPurchaseItem(): PurchaseItem
    {
        return $this->purchaseItem;
    }
}
