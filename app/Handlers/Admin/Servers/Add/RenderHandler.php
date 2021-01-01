<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Servers\Add;

use app\DataTransferObjects\Admin\Servers\Add\RenderResult;
use Illuminate\Contracts\Config\Repository;

class RenderHandler
{
    /**
     * @var Repository
     */
    private $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function handle(): RenderResult
    {
        return new RenderResult($this->config->get('purchasing.distribution.distributors'));
    }
}
