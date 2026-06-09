<?php

declare(strict_types=1);

require __DIR__ . '/helpers.php';

use NepaliCalendar\BsToAd\BsToAd;

$bsToAd = new BsToAd();

echo PHP_EOL . 'BS => AD (date only)' . PHP_EOL;

$ad = $bsToAd->convertDate(2067, 11, 27);
echo 'BS 2067-11-27 => AD ' . formatDateParts($ad) . PHP_EOL;

$adAnchor = $bsToAd->convertDate(2000, 1, 1);
echo 'BS 2000-01-01 => AD (range start) ' . formatDateParts($adAnchor) . PHP_EOL;

echo PHP_EOL . 'BS => AD (Nepal wall clock)' . PHP_EOL;

$adLocal = $bsToAd->convertLocalDateTime(2067, 11, 27, 12, 34, 56);
echo 'BS 2067-11-27 12:34:56 => AD ' . formatDateTimeParts($adLocal) . PHP_EOL;

echo PHP_EOL . 'Current AD datetime (Nepal +05:45)' . PHP_EOL;

$adNow = $bsToAd->getEnglishDateTime();
echo 'AD now ' . formatDateTimeParts($adNow) . PHP_EOL;

echo PHP_EOL;
