<?php
declare(strict_types = 1);

namespace app\Exceptions\News;

use app\Exceptions\DomainException;

class NewsNotFoundException extends DomainException
{
    public static function byId(int $id): NewsNotFoundException
    {
        return new NewsNotFoundException("News with id {$id} not found");
    }
}
