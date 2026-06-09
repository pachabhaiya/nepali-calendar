<?php

declare(strict_types=1);

namespace NepaliCalendar\AdToBs;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use NepaliCalendar\Data\BsMonthLengths;
use OutOfRangeException;
use InvalidArgumentException;

final class AdToBs implements AdToBsInterface
{
    /** Minimum supported BS year. */
    public const MIN_BS_YEAR = BsMonthLengths::MIN_YEAR;

    /** Maximum supported BS year. */
    public const MAX_BS_YEAR = BsMonthLengths::MAX_YEAR;

    /** Fixed Nepal offset (+05:45); avoids Asia/Kathmandu pre-1986 drift. */
    public const NEPAL_TZ = '+05:45';

    /** AD date of BS 2000-01-01 in Nepal. */
    private const BASE_AD = '1943-04-14';

    /** BS month names (1–12). */
    private const MONTH_NAMES = [
        1  => 'Baishakh',
        2  => 'Jestha',
        3  => 'Ashadh',
        4  => 'Shrawan',
        5  => 'Bhadra',
        6  => 'Ashwin',
        7  => 'Kartik',
        8  => 'Mangsir',
        9  => 'Poush',
        10 => 'Magh',
        11 => 'Falgun',
        12 => 'Chaitra',
    ];

    private static ?DateTimeZone $nepalTz = null;

    /** {@inheritDoc} */
    public function convertDate(int $adYear, int $adMonth, int $adDay): array
    {
        $this->validateAdDateParts($adYear, $adMonth, $adDay);

        $baseAd = new DateTimeImmutable(self::BASE_AD . ' 00:00:00', $this->getNepalTz());
        $targetAd = new DateTimeImmutable(
            sprintf('%04d-%02d-%02d 00:00:00', $adYear, $adMonth, $adDay),
            $this->getNepalTz()
        );

        // Whole days from the library AD anchor to the target Nepal AD date.
        $daysFromAnchor = (int) $baseAd->diff($targetAd)->format('%r%a');

        if ($daysFromAnchor < 0) {
            throw new OutOfRangeException('AD date is before supported range.');
        }

        $bsYear = self::MIN_BS_YEAR;
        $remainingDays = $daysFromAnchor;

        while (true) {
            if ($bsYear > self::MAX_BS_YEAR) {
                throw new OutOfRangeException('AD date is after supported range.');
            }

            $monthLengths = BsMonthLengths::monthsForYear($bsYear);
            foreach ($monthLengths as $bsMonth => $daysInMonth) {
                if ($remainingDays < $daysInMonth) {
                    $bsDay = $remainingDays + 1;

                    return ['year' => $bsYear, 'month' => $bsMonth, 'day' => $bsDay];
                }
                $remainingDays -= $daysInMonth;
            }

            $bsYear++;
        }
    }

    /** {@inheritDoc} */
    public function convertDateTime(DateTimeInterface $adDateTime): array
    {
        $dtNepal = (new DateTimeImmutable())
            ->setTimestamp($adDateTime->getTimestamp())
            ->setTimezone($this->getNepalTz());

        $bsDateParts = $this->convertDate(
            (int) $dtNepal->format('Y'),
            (int) $dtNepal->format('n'),
            (int) $dtNepal->format('j')
        );

        return $this->formatBsDateTime($dtNepal, $bsDateParts);
    }

    /** {@inheritDoc} */
    public function convertLocalDateTime(
        int $adYear,
        int $adMonth,
        int $adDay,
        int $hour = 0,
        int $minute = 0,
        int $second = 0
    ): array {
        $this->validateAdDateParts($adYear, $adMonth, $adDay);
        $this->validateTimeParts($hour, $minute, $second);

        $dtNepal = new DateTimeImmutable(
            sprintf('%04d-%02d-%02d %02d:%02d:%02d', $adYear, $adMonth, $adDay, $hour, $minute, $second),
            $this->getNepalTz()
        );

        return $this->convertDateTime($dtNepal);
    }

    /** {@inheritDoc} */
    public function getNepaliDateTime(?int $timestamp = null): array
    {
        $dtNepal = (new DateTimeImmutable('now'))
            ->setTimezone($this->getNepalTz());

        if ($timestamp !== null) {
            $dtNepal = $dtNepal->setTimestamp($timestamp);
        }

        $bsDateParts = $this->convertDate(
            (int) $dtNepal->format('Y'),
            (int) $dtNepal->format('n'),
            (int) $dtNepal->format('j')
        );

        return $this->formatBsDateTime($dtNepal, $bsDateParts);
    }

    /**
     * BS month number (1–12) to English name.
     *
     * @throws InvalidArgumentException
     */
    private function getMonthName(int $month): string
    {
        if (!isset(self::MONTH_NAMES[$month])) {
            throw new InvalidArgumentException('Month must be in the range 1..12.');
        }

        return self::MONTH_NAMES[$month];
    }

    /**
     * Build the library BS datetime array from date parts and Nepal wall clock.
     *
     * @param array{year:int,month:int,day:int} $bsDateParts
     *
     * @return array{Y:int,n:int,m:string,F:string,j:int,d:string,H:string,i:string,s:string,w:int,l:string}
     */
    private function formatBsDateTime(DateTimeImmutable $dtNepal, array $bsDateParts): array
    {
        $bsMonth = $bsDateParts['month'];
        $bsDay = $bsDateParts['day'];

        return [
            'Y' => $bsDateParts['year'],
            'n' => $bsMonth,
            'm' => str_pad((string) $bsMonth, 2, '0', STR_PAD_LEFT),
            'F' => $this->getMonthName($bsMonth),
            'j' => $bsDay,
            'd' => str_pad((string) $bsDay, 2, '0', STR_PAD_LEFT),
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
     * Validate Gregorian date parts.
     *
     * @throws InvalidArgumentException
     */
    private function validateAdDateParts(int $year, int $month, int $day): void
    {
        if (!checkdate($month, $day, $year)) {
            throw new InvalidArgumentException("Invalid AD date: {$year}-{$month}-{$day}.");
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
