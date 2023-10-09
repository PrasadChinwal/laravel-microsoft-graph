<?php

namespace PrasadChinwal\MicrosoftGraph\Collections;

use Illuminate\Support\Collection;
use PrasadChinwal\MicrosoftGraph\Response\Calendar;

class CalendarCollection extends Collection
{
    public static function createFromArray(array $data): CalendarCollection
    {
        $calendars = [];

        foreach ($data as $item) {
            $calendars[] = new Calendar($item);
        }

        return new self($calendars);
    }
}
