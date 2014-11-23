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

	public function testEmpty() {
		$stack = array();
		$this->assertEmpty($stack);

		array_push($stack, 'one');

		return $stack;
	}

	/**
   * @depends testEmpty
   */
  public function testPush(array $stack)
  {

  		// print_r(func_get_args());
      array_push($stack, 'foo');
      $this->assertEquals('foo', $stack[count($stack)-1]);
      $this->assertNotEmpty($stack);

      return $stack;
  }


  public function inputNumbers()
  {
    return array(
      array(0, 0, 0),
      array(0, 1, 1)
    );
  }

  /**
   * @dataProvider inputNumbers
   */
  public function testCanAddNumbers($x, $y, $sum)
  {
    $this->assertEquals($sum, $x + $y);
  }




  /**
   * @depends testPush
   */
  // public function testPop(array $stack)
  // {
  //     $this->assertEquals('foo', array_pop($stack));
  //     $this->assertEmpty($stack);
  // }
}
