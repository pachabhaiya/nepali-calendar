<?php

declare(strict_types=1);

namespace NepaliCalendar\BsToAd;

interface BsToAdInterface
{
    /**
     * Convert BS to AD (date only).
     *
     * @return array{year:int,month:int,day:int}
     *
     * @throws \InvalidArgumentException
     * @throws \OutOfRangeException
     */
    public function convertDate(int $bsYear, int $bsMonth, int $bsDay): array;

    /**
     * Convert BS date/time given as Nepal wall clock (+05:45) to AD.
     *
     * @return array{Y:int,n:int,m:string,j:int,d:string,H:string,i:string,s:string,w:int,l:string}
     *
     * @throws \InvalidArgumentException
     * @throws \OutOfRangeException
     */
    public function convertLocalDateTime(
        int $bsYear,
        int $bsMonth,
        int $bsDay,
        int $hour = 0,
        int $minute = 0,
        int $second = 0
    ): array;

    /**
     * Current or given Unix timestamp as AD datetime in Nepal.
     *
     * @return array{Y:int,n:int,m:string,j:int,d:string,H:string,i:string,s:string,w:int,l:string}
     *
     * @throws \OutOfRangeException
     */
    public function getEnglishDateTime(?int $timestamp = null): array;
}
