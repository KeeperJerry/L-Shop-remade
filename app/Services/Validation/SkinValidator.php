<?php
declare(strict_types = 1);

namespace App\Services\Validation;

/**
 * Class SkinValidator
 * Used to validate the size of the skin image.
 */
class SkinValidator
{
    /**
     * Validate skin image sizes.
     *
     * @param int $width Image width.
     * @param int $height Image height.
     *
     * @return bool True - parameters is valid.
     */
    public function validate(int $width, int $height): bool
    {
        $ratio = (int)($width / 64);

        $validWidth = $width / $ratio === 64;
		
		// НедоФикс для поддержки скинов 64x64
		if ($height / $ratio === 32)
		{
			$validHeight = $height / $ratio === 32;
		}
			
		if ($height / $ratio === 64)
		{
			$validHeight = $height / $ratio === 64;
		}

        return $validWidth && $validHeight;
    }
}
