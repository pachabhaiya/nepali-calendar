<?php

use NepaliCalendar\NepaliCalendar;
use NepaliCalendar\AdToBs\AdToBs;
use NepaliCalendar\BsToAd\BsToAd;

class NepaliCalendarTest extends PHPUnit_Framework_TestCase {

	private $nepali_calendar;

	public function setUp() {
		$this->nepali_calendar = new NepaliCalendar(new AdToBs(), new BsToAd());
	}

	public function testDate() {
		$today = $this->nepali_calendar->getCurrentDate();
		// print_r($this->nepali_calendar->getCurrentDate());
		$this->assertEquals($today['Y'], '2071');
	}

}
