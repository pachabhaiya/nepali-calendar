# Nepali Calendar

A PHP library for converting dates and times between the Gregorian (AD) and Bikram Sambat (BS) calendars, with datetime support in Nepal's fixed UTC+05:45 offset.

## Requirements

- PHP **≥ 8.2**

## Installation

```bash
composer require pachabhaiya/nepali-calendar
```

## Supported range

| Calendar | Minimum    | Maximum    |
| -------- | ---------- | ---------- |
| BS       | 2000-01-01 | 2090-12-30 |
| AD       | 1943-04-14 | ~2033-04   |

Dates outside this range throw `\OutOfRangeException`.

**Anchor:** BS 2000-01-01 = AD 1943-04-14 (Nepal civil date).

All arithmetic uses the fixed offset `+05:45` (`AdToBs::NEPAL_TZ` / `BsToAd::NEPAL_TZ`), not PHP's `Asia/Kathmandu` zone, which used UTC+05:30 before 1986.

## Quick start

```php
use NepaliCalendar\AdToBs\AdToBs;
use NepaliCalendar\BsToAd\BsToAd;
use NepaliCalendar\NepaliCalendar;

$adToBs = new AdToBs();
$bsToAd = new BsToAd();
$calendar = new NepaliCalendar($adToBs, $bsToAd);

// AD → BS (date only)
$bs = $adToBs->convertDate(2011, 3, 11);
// ['year' => 2067, 'month' => 11, 'day' => 27]

// BS → AD (date only)
$ad = $bsToAd->convertDate(2050, 3, 4);
// ['year' => 1993, 'month' => 6, 'day' => 18]

// Current BS datetime in Nepal
$today = $calendar->getCurrentDateTime();
// ['Y' => 2082, 'n' => 1, 'm' => '01', 'F' => 'Baishakh', 'j' => 21, ...]
```

## Usage

### AD → BS (date only)

```php
use NepaliCalendar\AdToBs\AdToBs;

$adToBs = new AdToBs();

$bs = $adToBs->convertDate(2011, 3, 11);
// ['year' => 2067, 'month' => 11, 'day' => 27]
```

Month names appear on datetime results as the `F` key (see [return shapes](#return-shapes)).

### BS → AD (date only)

```php
use NepaliCalendar\BsToAd\BsToAd;

$bsToAd = new BsToAd();

$ad = $bsToAd->convertDate(2050, 3, 4);
// ['year' => 1993, 'month' => 6, 'day' => 18]
```

### Current BS datetime

```php
use NepaliCalendar\AdToBs\AdToBs;

$adToBs = new AdToBs();

$today = $adToBs->getNepaliDateTime();

// For a specific Unix timestamp:
$bs = $adToBs->getNepaliDateTime(time());
```

### AD → BS datetime

`convertDateTime()` accepts a `DateTimeInterface` in any timezone. The BS date follows the Nepal UTC+05:45 civil date for that instant; time fields use the Kathmandu wall clock.

```php
use DateTimeImmutable;
use DateTimeZone;
use NepaliCalendar\AdToBs\AdToBs;

$adToBs = new AdToBs();

$bs = $adToBs->convertDateTime(
    new DateTimeImmutable('2011-03-11 12:34:56', new DateTimeZone('UTC'))
);
// ['Y' => 2067, 'n' => 11, 'F' => 'Falgun', 'j' => 27, 'H' => '18', 'i' => '19', 's' => '56', ...]
```

When you already have Nepal wall-clock parts, use `convertLocalDateTime()`:

```php
$bs = $adToBs->convertLocalDateTime(2011, 3, 11, 12, 34, 56);
```

### BS → AD datetime

```php
use NepaliCalendar\BsToAd\BsToAd;

$bsToAd = new BsToAd();

$ad = $bsToAd->convertLocalDateTime(2050, 3, 4, 12, 34, 56);
// ['Y' => 1993, 'n' => 6, 'j' => 18, 'H' => '12', 'i' => '34', 's' => '56', ...]

// Current AD datetime in Nepal from a Unix timestamp:
$adNow = $bsToAd->getEnglishDateTime(time());
```

### `NepaliCalendar` facade

```php
use NepaliCalendar\AdToBs\AdToBs;
use NepaliCalendar\BsToAd\BsToAd;
use NepaliCalendar\NepaliCalendar;

$calendar = new NepaliCalendar(new AdToBs(), new BsToAd());

$calendar->getCurrentDateTime();
$calendar->convertDate('ad_to_bs', 2011, 3, 11);
$calendar->convertDate('bs_to_ad', 2050, 3, 4);
$calendar->convertAdToBsDateTime($dateTime);
$calendar->convertAdToBsLocalDateTime(2011, 3, 11, 12, 34, 56);
$calendar->convertBsToAdDateTime(2050, 3, 4, 12, 34, 56);
```

## Return shapes

### Date-only (`convertDate`)

| Key    | Type | Example |
| ------ | ---- | ------- |
| `year` | int  | `2067`  |
| `month`| int  | `11`    |
| `day`  | int  | `27`    |

### BS datetime

| Key | Type   | Description                            |
| --- | ------ | -------------------------------------- |
| `Y` | int    | BS year                                |
| `n` | int    | BS month (no leading zero)             |
| `m` | string | BS month (zero-padded)                 |
| `F` | string | BS month name (e.g. `Bhadra`)          |
| `j` | int    | BS day (no leading zero)               |
| `d` | string | BS day (zero-padded)                   |
| `H` | string | Hour, 24-hour (Nepal +05:45)           |
| `i` | string | Minute                                 |
| `s` | string | Second                                 |
| `w` | int    | Weekday (0 = Sunday … 6 = Saturday)    |
| `l` | string | Weekday name                           |

### AD datetime (`convertLocalDateTime`, `getEnglishDateTime`)

Same keys as BS datetime, but values are Gregorian (AD) in Nepal local time. No `F` key.

## Error handling

| Exception                   | When                                              |
| --------------------------- | ------------------------------------------------- |
| `\InvalidArgumentException` | Invalid date/time parts or unsupported `$type`    |
| `\OutOfRangeException`      | BS year outside 2000–2090, or AD before anchor    |

```php
try {
    $adToBs->convertDate(2023, 2, 30);
} catch (\InvalidArgumentException $e) {
    // Invalid AD date
}

try {
    $adToBs->convertDate(1900, 1, 1);
} catch (\OutOfRangeException $e) {
    // Before supported range
}
```

## Architecture

```
src/
├── NepaliCalendar.php              Facade (implements NepaliCalendarInterface)
├── NepaliCalendarInterface.php
├── AdToBs/
│   ├── AdToBsInterface.php
│   └── AdToBs.php                  AD → BS conversion
├── BsToAd/
│   ├── BsToAdInterface.php
│   └── BsToAd.php                  BS → AD conversion
└── Data/
    └── BsMonthLengths.php          Month-length table for BS 2000–2090
```

**BS → AD:** Days from the BS anchor are summed via `BsMonthLengths`, then added to the AD anchor (1943-04-14).

**AD → BS:** Signed day difference from the AD anchor is walked through the month-length table to resolve BS year, month, and day.

**Datetime:** Civil date comes from the Nepal +05:45 wall clock; time fields match that same local instant.

## Examples

Runnable scripts under `examples/`:

```bash
php examples/ad_to_bs.php
php examples/bs_to_ad.php
```

## Development

```bash
composer install
./vendor/bin/phpunit
```

## License

MIT © [Chhabi Pachabhaiya](https://chhabi.dev)
