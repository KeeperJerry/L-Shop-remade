<?php
declare(strict_types = 1);

namespace app\Services\Purchasing\Payers;

/**
 * Class Pool
 * Keep payer registered in the system.
 */
class Pool
{
    /**
     * @var Payer[]
     */
    private $payers;

    /**
     * Pool constructor.
     *
     * @param Payer[] $payers
     */
    public function __construct(array $payers)
    {
        $this->payers = $payers;
    }

    public function retrieveByName(string $name): ?Payer
    {
        foreach ($this->payers as $payer) {
            if ($payer->name() === $name) {
                return $payer;
            }
        }

        return null;
    }

    /**
     * @return Payer[]
     */
    public function all(): array
    {
        return $this->payers;
    }

    /**
     * @return Payer[]
     */
    public function allEnabled(): array
    {
        $result = [];
        foreach ($this->payers as $payer) {
            if ($payer->enabled()) {
                $result[] = $payer;
            }
        }

        return $result;
    }
}
