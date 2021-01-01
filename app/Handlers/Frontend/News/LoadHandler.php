<?php
declare(strict_types = 1);

namespace app\Handlers\Frontend\News;

use app\DataTransferObjects\Frontend\Shop\News\Container;
use app\DataTransferObjects\Frontend\Shop\News\Item;
use app\Repository\News\NewsRepository;
use app\Services\Settings\DataType;
use app\Services\Settings\Settings;

class LoadHandler
{
    /**
     * @var NewsRepository
     */
    private $newsRepository;

    /**
     * @var Settings
     */
    private $settings;

    public function __construct(NewsRepository $newsRepository, Settings $settings)
    {
        $this->newsRepository = $newsRepository;
        $this->settings = $settings;
    }

    public function load(int $portion): Container
    {
        $paginator = $this->newsRepository->findPaginatedOrderByCreatedAtDesc(
            $portion, $this->settings->get('system.news.pagination.per_page')->getValue(DataType::INT));

        $items = $paginator->items();
        $result = [];
        foreach ($items as $item) {
            $result[] = new Item($item);
        }

        return new Container($result, $paginator->total(), $portion);
    }
}
