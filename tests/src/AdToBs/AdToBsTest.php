<?php

declare(strict_types=1);

namespace NepaliCalendar\Tests\AdToBs;

use DateTimeImmutable;
use DateTimeZone;
use NepaliCalendar\AdToBs\AdToBs;
use NepaliCalendar\BsToAd\BsToAd;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AdToBs::class)]
final class AdToBsTest extends TestCase
{
    private AdToBs $adToBs;

    protected function setUp(): void
    {
        $this->adToBs = new AdToBs();
    }

    public function testConvertDateMapsKnownReferenceDate(): void
    {
        $bs = $this->adToBs->convertDate(2011, 3, 11);

        $this->assertSameBsDateParts(2067, 11, 27, $bs);
    }

    public function testConvertDateMapsRangeStartAnchor(): void
    {
        $bs = $this->adToBs->convertDate(1943, 4, 14);

        $this->assertSameBsDateParts(2000, 1, 1, $bs);
    }

    public function testConvertDateTimeUsesKathmanduCivilDate(): void
    {
        $adUtc = new DateTimeImmutable('2011-03-10 18:16:00', new DateTimeZone('UTC'));
        $bs = $this->adToBs->convertDateTime($adUtc);

        $this->assertSameBsDateTimeCalendar(2067, 11, 27, $bs);
    }

    public function testConvertLocalDateTimeIncludesMonthNameAndWallClock(): void
    {
        $bs = $this->adToBs->convertLocalDateTime(2011, 3, 11, 12, 34, 56);

        $this->assertSame('Falgun', $bs['F']);
        $this->assertSameBsDateTimeCalendar(2067, 11, 27, $bs);
        $this->assertSame('12', $bs['H']);
        $this->assertSame('34', $bs['i']);
        $this->assertSame('56', $bs['s']);
    }

    public function testRoundTripThroughBsToAd(): void
    {
        $bs = $this->adToBs->convertDate(2011, 3, 11);
        $ad = (new BsToAd())->convertDate($bs['year'], $bs['month'], $bs['day']);

        $this->assertSameAdDateParts(2011, 3, 11, $ad);
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
     * @param array{Y:int,n:int,j:int,...} $bs
     */
    private function assertSameBsDateTimeCalendar(int $year, int $month, int $day, array $bs): void
    {
        $this->assertSame($year, $bs['Y']);
        $this->assertSame($month, $bs['n']);
        $this->assertSame($day, $bs['j']);
    }
}
