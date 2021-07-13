<?php

namespace NepaliCalendar\AdToBs;

interface AdToBsInterface
{
    public function getNepaliDate($timestamp = null);
    public function month($month);
}
