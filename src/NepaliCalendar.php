<?php

declare(strict_types=1);

namespace NepaliCalendar;

use DateTimeInterface;
use NepaliCalendar\AdToBs\AdToBs;
use NepaliCalendar\BsToAd\BsToAd;

class NepaliCalendar implements NepaliCalendarInterface
{
    /** {@inheritDoc} */
    public function __construct(
        private AdToBs $adToBs,
        private BsToAd $bsToAd,
    ) {}

    /** {@inheritDoc} */
    public function getCurrentDateTime(): array
    {
        return $this->adToBs->getNepaliDateTime();
    }

    /** {@inheritDoc} */
    public function convertDate(string $type, int $year, int $month, int $day): array
    {
        return match ($type) {
            'ad_to_bs' => $this->adToBs->convertDate($year, $month, $day),
            'bs_to_ad' => $this->bsToAd->convertDate($year, $month, $day),
            default    => throw new \InvalidArgumentException(
                "Unsupported conversion type \"{$type}\". Use 'ad_to_bs' or 'bs_to_ad'."
            ),
        };
    }

    /** {@inheritDoc} */
    public function convertAdToBsDateTime(DateTimeInterface $adDateTime): array
    {
        return $this->adToBs->convertDateTime($adDateTime);
    }

    /** {@inheritDoc} */
    public function convertAdToBsLocalDateTime(
        int $adYear,
        int $adMonth,
        int $adDay,
        int $hour = 0,
        int $minute = 0,
        int $second = 0
    ): array {
        return $this->adToBs->convertLocalDateTime($adYear, $adMonth, $adDay, $hour, $minute, $second);
    }

    /** {@inheritDoc} */
    public function convertBsToAdDateTime(
        int $bsYear,
        int $bsMonth,
        int $bsDay,
        int $hour = 0,
        int $minute = 0,
        int $second = 0
    ): array {
        return $this->bsToAd->convertLocalDateTime($bsYear, $bsMonth, $bsDay, $hour, $minute, $second);
    }
}
