<?php

declare(strict_types=1);

namespace NepaliCalendar;

use DateTimeInterface;
use NepaliCalendar\AdToBs\AdToBs;
use NepaliCalendar\BsToAd\BsToAd;

interface NepaliCalendarInterface
{
    public function __construct(AdToBs $adToBs, BsToAd $bsToAd);

    /**
     * Current BS datetime in Nepal (+05:45).
     *
     * @return array{Y:int,n:int,m:string,F:string,j:int,d:string,H:string,i:string,s:string,w:int,l:string}
     *
     * @throws \OutOfRangeException
     */
    public function getCurrentDateTime(): array;

    /**
     * Convert AD↔BS (date only). $type is 'ad_to_bs' or 'bs_to_ad'.
     *
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException
     * @throws \OutOfRangeException
     */
    public function convertDate(string $type, int $year, int $month, int $day): array;

    /**
     * AD instant to BS (Nepal local date and time).
     *
     * @return array{Y:int,n:int,m:string,F:string,j:int,d:string,H:string,i:string,s:string,w:int,l:string}
     *
     * @throws \InvalidArgumentException
     * @throws \OutOfRangeException
     */
    public function convertAdToBsDateTime(DateTimeInterface $adDateTime): array;

    /**
     * AD date/time as Nepal wall clock to BS.
     *
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException
     * @throws \OutOfRangeException
     */
    public function convertAdToBsLocalDateTime(
        int $adYear,
        int $adMonth,
        int $adDay,
        int $hour = 0,
        int $minute = 0,
        int $second = 0
    ): array;

    /**
     * BS date/time as Nepal wall clock to AD.
     *
     * @return array{Y:int,n:int,m:string,j:int,d:string,H:string,i:string,s:string,w:int,l:string}
     *
     * @throws \InvalidArgumentException
     * @throws \OutOfRangeException
     */
    public function convertBsToAdDateTime(
        int $bsYear,
        int $bsMonth,
        int $bsDay,
        int $hour = 0,
        int $minute = 0,
        int $second = 0
    ): array;
}
