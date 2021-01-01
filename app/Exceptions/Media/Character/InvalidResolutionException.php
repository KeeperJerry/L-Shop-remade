<?php
declare(strict_types = 1);

namespace app\Exceptions\Media\Character;

use app\Exceptions\Media\ImageException;

class InvalidResolutionException extends ImageException
{
    public function __construct(int $width, int $height)
    {
        parent::__construct(
            "Image with width: {$width} and height: {$height} invalid", 0, null
        );
    }
}
