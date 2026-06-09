<?php

declare(strict_types=1);

namespace NepaliCalendar\Tests\BsToAd;

use NepaliCalendar\AdToBs\AdToBs;
use NepaliCalendar\BsToAd\BsToAd;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BsToAd::class)]
final class BsToAdTest extends TestCase
{
    private BsToAd $bsToAd;

    protected function setUp(): void
    {
        $this->bsToAd = new BsToAd();
    }

    public function testConvertDateMapsKnownReferenceDate(): void
    {
        $ad = $this->bsToAd->convertDate(2050, 3, 4);

        $this->assertSameAdDateParts(1993, 6, 18, $ad);
    }

    public function testConvertDateMapsRangeStartAnchor(): void
    {
        $ad = $this->bsToAd->convertDate(2000, 1, 1);

        $this->assertSameAdDateParts(1943, 4, 14, $ad);
    }

    public function testConvertLocalDateTimePreservesKathmanduWallClock(): void
    {
        $ad = $this->bsToAd->convertLocalDateTime(2050, 3, 4, 12, 34, 56);

        $this->assertSameAdDateTimeCalendar(1993, 6, 18, $ad);
        $this->assertSame('12', $ad['H']);
        $this->assertSame('34', $ad['i']);
        $this->assertSame('56', $ad['s']);
    }

    public function testRoundTripThroughAdToBs(): void
    {
        $ad = $this->bsToAd->convertDate(2050, 3, 4);
        $bs = (new AdToBs())->convertDate($ad['year'], $ad['month'], $ad['day']);

        $this->assertSameBsDateParts(2050, 3, 4, $bs);
    }

    /**
     * @param array{year:int,month:int,day:int} $actual
     */
    private function assertSameAdDateParts(int $year, int $month, int $day, array $actual): void
    {
        $this->assertSame(
            ['year' => $year, 'month' => $month, 'day' => $day],
            $actual
        );
    }

    /**
     * @param array{year:int,month:int,day:int} $actual
     */
    private function assertSameBsDateParts(int $year, int $month, int $day, array $actual): void
    {
        $this->assertSame(
            ['year' => $year, 'month' => $month, 'day' => $day],
            $actual
        );
    }

    /**
     * @param array{Y:int,n:int,j:int,...} $ad
     */
    private function assertSameAdDateTimeCalendar(int $year, int $month, int $day, array $ad): void
    {
        $this->assertSame($year, $ad['Y']);
        $this->assertSame($month, $ad['n']);
        $this->assertSame($day, $ad['j']);
    }
}
