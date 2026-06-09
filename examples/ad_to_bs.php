<?php

declare(strict_types=1);

require __DIR__ . '/helpers.php';

use NepaliCalendar\AdToBs\AdToBs;

$adToBs = new AdToBs();

echo PHP_EOL . 'AD => BS (date only)' . PHP_EOL;

$bs = $adToBs->convertDate(2011, 3, 11);
echo 'AD 2011-03-11 => BS ' . formatDateParts($bs) . PHP_EOL;

$bsAnchor = $adToBs->convertDate(1943, 4, 14);
echo 'AD 1943-04-14 => BS (range start) ' . formatDateParts($bsAnchor) . PHP_EOL;

echo PHP_EOL . 'AD => BS (datetime from instant)' . PHP_EOL;

$utc = new \DateTimeImmutable('2011-03-11 12:34:56', new \DateTimeZone('UTC'));
$bsFromUtc = $adToBs->convertDateTime($utc);
echo 'UTC 2011-03-11 12:34:56 => BS ' . formatDateTimeParts($bsFromUtc) . PHP_EOL;

$chicago = new \DateTimeImmutable('2011-03-11 12:34:56', new \DateTimeZone('America/Chicago'));
$bsFromChicago = $adToBs->convertDateTime($chicago);
echo 'Chicago 2011-03-11 12:34:56 => BS ' . formatDateTimeParts($bsFromChicago) . PHP_EOL;

echo PHP_EOL . 'AD => BS (Nepal wall clock)' . PHP_EOL;

$bsLocal = $adToBs->convertLocalDateTime(2011, 3, 11, 12, 34, 56);
echo 'AD +05:45 2011-03-11 12:34:56 => BS ' . formatDateTimeParts($bsLocal) . PHP_EOL;

echo PHP_EOL . 'Current BS datetime (Nepal +05:45)' . PHP_EOL;

$today = $adToBs->getNepaliDateTime();
echo 'BS now ' . formatDateTimeParts($today) . PHP_EOL;

echo PHP_EOL;
