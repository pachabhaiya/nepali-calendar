<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

/**
 * @param array{year:int,month:int,day:int} $parts
 */
function formatDateParts(array $parts): string
{
    return sprintf('%d-%02d-%02d', $parts['year'], $parts['month'], $parts['day']);
}

/**
 * @param array{Y:int,n:int,j:int,H:string,i:string,s:string,F?:string,l:string} $parts
 */
function formatDateTimeParts(array $parts): string
{
    $date = sprintf('%d-%02d-%02d', $parts['Y'], $parts['n'], $parts['j']);
    $time = sprintf('%s:%s:%s', $parts['H'], $parts['i'], $parts['s']);
    $month = isset($parts['F']) ? " ({$parts['F']})" : '';

    return "{$date} {$time}{$month}, {$parts['l']}";
}
