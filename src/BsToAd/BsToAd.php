<?php

declare(strict_types=1);

namespace NepaliCalendar\BsToAd;

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use NepaliCalendar\Data\BsMonthLengths;
use OutOfRangeException;

final class BsToAd implements BsToAdInterface
{
    /** Minimum supported BS year. */
    public const MIN_BS_YEAR = BsMonthLengths::MIN_YEAR;

    /** Maximum supported BS year. */
    public const MAX_BS_YEAR = BsMonthLengths::MAX_YEAR;

    /** Fixed Nepal offset (+05:45); avoids Asia/Kathmandu pre-1986 drift. */
    public const NEPAL_TZ = '+05:45';

    /** AD date of BS 2000-01-01 in Nepal. */
    private const BASE_AD = '1943-04-14';

    private static ?DateTimeZone $nepalTz = null;

    /** {@inheritDoc} */
    public function convertDate(int $bsYear, int $bsMonth, int $bsDay): array
    {
        $this->validateBsDateParts($bsYear, $bsMonth, $bsDay);

        $daysOffset = $this->daysOffsetFromBaseBsDate($bsYear, $bsMonth, $bsDay);
        $base = new DateTimeImmutable(self::BASE_AD . ' 00:00:00', $this->getNepalTz());
        $ad = $base->add(new DateInterval('P' . $daysOffset . 'D'));

        return [
            'year' => (int) $ad->format('Y'),
            'month' => (int) $ad->format('n'),
            'day' => (int) $ad->format('j'),
        ];
    }

    /** {@inheritDoc} */
    public function convertLocalDateTime(
        int $bsYear,
        int $bsMonth,
        int $bsDay,
        int $hour = 0,
        int $minute = 0,
        int $second = 0
    ): array {
        $this->validateBsDateParts($bsYear, $bsMonth, $bsDay);
        $this->validateTimeParts($hour, $minute, $second);

        $adDateParts = $this->convertDate($bsYear, $bsMonth, $bsDay);
        $dtNepal = new DateTimeImmutable(
            sprintf(
                '%04d-%02d-%02d %02d:%02d:%02d',
                $adDateParts['year'],
                $adDateParts['month'],
                $adDateParts['day'],
                $hour,
                $minute,
                $second
            ),
            $this->getNepalTz()
        );

        return $this->formatAdDateTime($dtNepal);
    }

    /** {@inheritDoc} */
    public function getEnglishDateTime(?int $timestamp = null): array
    {
        $dtNepal = (new DateTimeImmutable('now'))
            ->setTimezone($this->getNepalTz());

        if ($timestamp !== null) {
            $dtNepal = $dtNepal->setTimestamp($timestamp);
        }

        return $this->formatAdDateTime($dtNepal);
    }

    /**
     * Build the library AD datetime array from Nepal wall clock.
     *
     * @return array{Y:int,n:int,m:string,j:int,d:string,H:string,i:string,s:string,w:int,l:string}
     */
    private function formatAdDateTime(DateTimeImmutable $dtNepal): array
    {
        return [
            'Y' => (int) $dtNepal->format('Y'),
            'n' => (int) $dtNepal->format('n'),
            'm' => $dtNepal->format('m'),
            'j' => (int) $dtNepal->format('j'),
            'd' => $dtNepal->format('d'),
            'H' => $dtNepal->format('H'),
            'i' => $dtNepal->format('i'),
            's' => $dtNepal->format('s'),
            'w' => (int) $dtNepal->format('w'),
            'l' => $dtNepal->format('l'),
        ];
    }

    /** Cached fixed-offset Nepal timezone ({@see NEPAL_TZ}). */
    private function getNepalTz(): DateTimeZone
    {
        return self::$nepalTz ??= new DateTimeZone(self::NEPAL_TZ);
    }

    /**
     * Whole days from BS 2000-01-01 to the given BS date.
     *
     * @return int Non-negative day offset from the anchor date.
     */
    private function daysOffsetFromBaseBsDate(int $bsYear, int $bsMonth, int $bsDay): int
    {
        $daysOffset = 0;

        for ($y = self::MIN_BS_YEAR; $y < $bsYear; $y++) {
            $daysOffset += array_sum(BsMonthLengths::monthsForYear($y));
        }

        $months = BsMonthLengths::monthsForYear($bsYear);
        for ($m = 1; $m < $bsMonth; $m++) {
            $daysOffset += $months[$m];
        }

        return $daysOffset + ($bsDay - 1);
    }

    /**
     * Validate BS date parts against supported range and month lengths.
     *
     * @throws OutOfRangeException
     * @throws InvalidArgumentException
     */
    private function validateBsDateParts(int $year, int $month, int $day): void
    {
        if ($year < self::MIN_BS_YEAR || $year > self::MAX_BS_YEAR) {
            throw new OutOfRangeException('Supported BS range is ' . self::MIN_BS_YEAR . '..' . self::MAX_BS_YEAR . '.');
        }
        if ($month < 1 || $month > 12) {
            throw new InvalidArgumentException('BS month must be 1..12.');
        }

        $months = BsMonthLengths::monthsForYear($year);
        $maxDay = $months[$month];
        if ($day < 1 || $day > $maxDay) {
            throw new InvalidArgumentException("BS day must be 1..{$maxDay} for {$year}-{$month}.");
        }
    }

    /**
     * Validate time-of-day parts.
     *
     * @throws InvalidArgumentException
     */
    private function validateTimeParts(int $hour, int $minute, int $second): void
    {
        if ($hour < 0 || $hour > 23) {
            throw new InvalidArgumentException('Hour must be in the range 0..23.');
        }
        if ($minute < 0 || $minute > 59) {
            throw new InvalidArgumentException('Minute must be in the range 0..59.');
        }
        if ($second < 0 || $second > 59) {
            throw new InvalidArgumentException('Second must be in the range 0..59.');
        }
    }
}
