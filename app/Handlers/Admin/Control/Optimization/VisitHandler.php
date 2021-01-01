<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Control\Optimization;

use app\DataTransferObjects\Admin\Control\Optimization\VisitResult;
use app\Services\Settings\DataType;
use app\Services\Settings\Settings;

class VisitHandler
{
    /**
     * @var Settings
     */
    private $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function handle(): VisitResult
    {
        return (new VisitResult())
            ->setMonitoringTtl($this->settings->get('system.monitoring.rcon.ttl')->getValue(DataType::INT));
    }
}
