<?php
declare(strict_types=1);

namespace app\Exceptions\Media\Character;

use app\Exceptions\Media\ImageException;

class InvalidRatioException extends ImageException
{
    public function __construct(int $width, int $height)
    {
        parent::__construct(
            "Image with width: {$width} and height: {$height} has invalid ratio", 0, null
        );
    }
}
