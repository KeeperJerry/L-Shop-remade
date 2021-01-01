<?php
declare(strict_types = 1);

namespace app\Exceptions\Page;

use app\Exceptions\DomainException;

class PageNotFoundException extends DomainException
{
    public static function byId(int $id): PageNotFoundException
    {
        return new PageNotFoundException("Page with id {$id} not found");
    }

    public static function byUrl(string $url): PageNotFoundException
    {
        return new PageNotFoundException("Page with url \"{$url}\" not found");
    }
}
