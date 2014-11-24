<?php

/**
 * @file
 * Contains \Pachabhaiya\NepaliCalendar\NepaliCalendar.
 */

namespace Pachabhaiya\NepaliCalendar;

use Pachabhaiya\NepaliCalendar\AdToBs\AdToBs;
use Pachabhaiya\NepaliCalendar\BsToAd\BsToAd;

class NepaliCalendar {

    /**
     * Gregorian calendar (A.D.) to Bikram Sambat (B.S.) converter.
     *
     * @var \Pachabhaiya\NepaliCalendar\AdToBs;
     */
    protected $ad_to_bs;

    /**
     * Bikram Sambat (B.S.) to Gregorian calendar (A.D.) converter.
     *
     * @var \Pachabhaiya\NepaliCalendar\BsToAd;
     */
    protected $bs_to_ad;

    public function __construct() {
        $this->ad_to_bs = new AdToBs();
        $this->bs_to_ad = new BsToAd();
    }

    public function getCurrentDate() {
        return $this->ad_to_bs->getNepaliDate();
    }

    public function getDate($type = 'ad_to_bs') {
        if ($type == 'bs') {
            return $this->ad_to_bs->getNepaliDate();
        } else if ($type == 'ad_to_bs') {
            return array();
        } else {
            return new \Exception('Invalid entry');
        }
    }

    public function convertDate($type = 'ad_to_bs') {
        
    }
}
