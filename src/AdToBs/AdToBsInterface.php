<?php

declare(strict_types=1);

namespace NepaliCalendar\AdToBs;

use DateTimeInterface;

interface AdToBsInterface
{
    /**
     * Convert AD to BS (date only).
     *
     * @return array{year:int,month:int,day:int}
     *
     * @throws \InvalidArgumentException
     * @throws \OutOfRangeException
     */
    public function convertDate(int $adYear, int $adMonth, int $adDay): array;

    /**
     * Convert AD datetime to BS (Nepal local date and time).
     *
     * @return array{Y:int,n:int,m:string,F:string,j:int,d:string,H:string,i:string,s:string,w:int,l:string}
     *
     * @throws \InvalidArgumentException
     * @throws \OutOfRangeException
     */
    public function convertDateTime(DateTimeInterface $adDateTime): array;

    /**
     * Convert AD date/time given as Nepal wall clock (+05:45) to BS.
     *
     * @return array{Y:int,n:int,m:string,F:string,j:int,d:string,H:string,i:string,s:string,w:int,l:string}
     *
     * @throws \InvalidArgumentException
     * @throws \OutOfRangeException
     */
    public function convertLocalDateTime(
        int $adYear,
        int $adMonth,
        int $adDay,
        int $hour = 0,
        int $minute = 0,
        int $second = 0
    ): array;

    /**
     * Current or given Unix timestamp as BS datetime in Nepal.
     *
     * @return array{Y:int,n:int,m:string,F:string,j:int,d:string,H:string,i:string,s:string,w:int,l:string}
     *
     * @throws \OutOfRangeException
     */
    public function getNepaliDateTime(?int $timestamp = null): array;
}
