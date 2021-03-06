<?php
declare(strict_types = 1);

namespace app\Services\DateTime;

use app\Exceptions\InvalidArgumentException;

class DateTimeUtil
{
    /**
     * Private constructor because this class contains only static methods.
     */
    private function __construct()
    {
    }

    /**
     * Adds the specified number of minutes to the passed DateTime object.
     *
     * @param \DateTimeImmutable $dateTime
     * @param int                $minutes
     *
     * @return \DateTimeImmutable
     */
    public static function addMinutes(\DateTimeImmutable $dateTime, int $minutes): \DateTimeImmutable
    {
        if ($minutes < 0) {
            throw new InvalidArgumentException('Argument $minutes must be greater or equals 0, ' . $minutes . ' given');
        }
        $interval = \DateInterval::createFromDateString("{$minutes} minutes");

        return $dateTime->add($interval);
    }

    /**
     * Adds the specified interval to the current time.
     *
     * @param \DateInterval $interval
     *
     * @return \DateTimeImmutable
     */
    public static function nowAdd(\DateInterval $interval): \DateTimeImmutable
    {
        return (new \DateTimeImmutable())->add($interval);
    }

    /**
     * Adds the specified number of seconds to the current time.
     *
     * @param int $seconds
     *
     * @return \DateTimeImmutable
     */
    public static function nowAddSeconds(int $seconds): \DateTimeImmutable
    {
        if ($seconds < 0) {
            throw new InvalidArgumentException('Argument $seconds must be greater or equals 0, ' . $seconds . ' given');
        }
        $interval = \DateInterval::createFromDateString("{$seconds} seconds");

        return (new \DateTimeImmutable())->add($interval);
    }

    /**
     * Adds the specified number of minutes to the current time.
     *
     * @param int $minutes
     *
     * @return \DateTimeImmutable
     */
    public static function nowAddMinutes(int $minutes): \DateTimeImmutable
    {
        if ($minutes < 0) {
            throw new InvalidArgumentException('Argument $minutes must be greater or equals 0, ' . $minutes . ' given');
        }
        $interval = \DateInterval::createFromDateString("{$minutes} minutes");

        return (new \DateTimeImmutable())->add($interval);
    }

    /**
     * Takes from the current time a specified $interval.
     *
     * @param \DateInterval $interval
     *
     * @return \DateTimeImmutable
     */
    public static function nowSub(\DateInterval $interval): \DateTimeImmutable
    {
        return (new \DateTimeImmutable())->sub($interval);
    }

    /**
     * Takes from the current time a specified number of seconds.
     *
     * @param int $seconds
     *
     * @return \DateTimeImmutable
     */
    public static function nowSubSeconds(int $seconds): \DateTimeImmutable
    {
        if ($seconds < 0) {
            throw new InvalidArgumentException('Argument $seconds must be greater or equals 0, ' . $seconds . ' given');
        }
        $interval = \DateInterval::createFromDateString("{$seconds} seconds");

        return (new \DateTimeImmutable())->sub($interval);
    }

    /**
     * Takes from the current time a specified number of minutes.
     *
     * @param int $minutes
     *
     * @return \DateTimeImmutable
     */
    public static function nowSubMinutes(int $minutes): \DateTimeImmutable
    {
        if ($minutes < 0) {
            throw new InvalidArgumentException('Argument $minutes must be greater or equals 0, ' . $minutes . ' given');
        }
        $interval = \DateInterval::createFromDateString("{$minutes} minutes");

        return (new \DateTimeImmutable())->sub($interval);
    }

    public static function daysToSeconds(float $days): float
    {
        return $days * 86400;
    }
}
