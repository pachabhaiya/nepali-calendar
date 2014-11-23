<?php

namespace NepaliCalendar\AdToBs;


class AdToBsTest extends \PHPUnit_Framework_TestCase
{

    private $ad_to_bs;

    public function setUp()
    {
        $this->ad_to_bs = new AdToBs();
    }

    public function test_month_in_nepali()
    {
        $this->assertEquals($this->ad_to_bs->month(1), 'Baishakh', 'First Month in Nepali Calendar is Baishakh');
    }

    public function test_current_year_in_nepal()
    {
        $today = $this->ad_to_bs->getNepaliDate();
        $current_year = 2071;
        $this->assertEquals($today['Y'], $current_year);
    }
}
