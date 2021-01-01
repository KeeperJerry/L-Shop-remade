<?php
declare(strict_types = 1);

namespace app\DataTransferObjects\Admin\Control\Optimization;

use app\Services\Response\JsonRespondent;

class VisitResult implements JsonRespondent
{
    /**
     * @var int
     */
    private $monitoringTtl;

    /**
     * @param int $monitoringTtl
     *
     * @return VisitResult
     */
    public function setMonitoringTtl(int $monitoringTtl): VisitResult
    {
        $this->monitoringTtl = $monitoringTtl;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function response(): array
    {
        return [
            'monitoringTtl' => $this->monitoringTtl
        ];
    }
}
