<?php
declare(strict_types = 1);

namespace app\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This entity is the link between {@see \app\Entity\Enchantment} and the {@see \app\Entity\Item}.
 *
 * @ORM\Entity
 * @ORM\Table(name="enchantment_items")
 */
class EnchantmentItem
{
    /**
     * Enchantment item identifier.
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * The level at which the {@see \app\Entity\Item} is enchanted. There can not be greater than
     * {@see \app\Entity\Enchantment::maxLevel}.
     *
     * @ORM\Column(name="level", type="integer")
     */
    private $level;

    /**
     * Enchantment that overlaps an {@see \app\Entity\EnchantmentItem::item}.
     *
     * @ORM\ManyToOne(targetEntity="app\Entity\Enchantment", inversedBy="enchantmentItems")
     * @ORM\JoinColumn(name="enchantment_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $enchantment;

    /**
     * Enchanted item.
     *
     * @ORM\ManyToOne(targetEntity="app\Entity\Item")
     */
    private $item;

    /**
     * EnchantmentItem constructor.
     *
     * @param Enchantment $enchantment
     * @param int         $level
     */
    public function __construct(Enchantment $enchantment, int $level)
    {
        $this->enchantment = $enchantment;
        $this->level = $level;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $level
     *
     * @return EnchantmentItem
     */
    public function setLevel(int $level): EnchantmentItem
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @return Enchantment
     */
    public function getEnchantment(): Enchantment
    {
        return $this->enchantment;
    }

    /**
     * @param Item $item
     *
     * @return EnchantmentItem
     */
    public function setItem(Item $item): EnchantmentItem
    {
        $this->item = $item;

        return $this;
    }

    /**
     * @return EnchantmentItem
     */
    public function deleteItem(): EnchantmentItem
    {
        $this->item = null;

        return $this;
    }

    /**
     * @return Item
     */
    public function getItem(): Item
    {
        return $this->item;
    }
}
