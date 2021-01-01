<?php
declare(strict_types = 1);

namespace app\Handlers\Frontend\Shop\Page;

use app\DataTransferObjects\Frontend\Page as DTO;
use app\Exceptions\Page\PageNotFoundException;
use app\Repository\Page\PageRepository;

class VisitHandler
{
    /**
     * @var PageRepository
     */
    private $pageRepository;

    public function __construct(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    /**
     * Gets the data to render the static page.
     *
     * @param string $url
     *
     * @return DTO
     * @throws PageNotFoundException
     */
    public function handle(string $url): DTO
    {
        $page = $this->pageRepository->findByUrl($url);
        if ($page === null) {
            throw PageNotFoundException::byUrl($url);
        }

        return new DTO($page);
    }
}
