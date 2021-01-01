<?php
declare(strict_types = 1);

namespace app\Exceptions\Item;

use app\Exceptions\LogicException;

/**
 * Class InvalidTypeException
 * It is thrown out if the item is not of the {@see \app\Entity\Item::type} expected.
 */
class InvalidTypeException extends LogicException
{
    /**
     * InvalidTypeException constructor.
     *
     * @param string|string[] $expected Expected types of item.
     * @param string          $given    Given type of item.
     */
    public function __construct($expected, string $given)
    {
        if (is_array($expected)) {
            $expected = implode(', ', $expected);
        }

        parent::__construct("Expected items type(s): {$expected}; {$given} given");
    }
}
