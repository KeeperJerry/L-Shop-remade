<?php
declare(strict_types = 1);

namespace app\Services\DateTime\Formatting;

use DateTimeInterface;

/**
 * Interface Formatter
 * Format the date and time.
 */
interface Formatter
{
    /**
     * Formatting.
     *
     * @param DateTimeInterface $dateTime
     *
     * @return string
     */
    public function format(DateTimeInterface $dateTime): string;
}
