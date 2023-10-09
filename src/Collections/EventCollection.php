<?php

namespace PrasadChinwal\MicrosoftGraph\Collections;

use Illuminate\Support\Collection;
use PrasadChinwal\MicrosoftGraph\Response\Calendar;
use PrasadChinwal\MicrosoftGraph\Response\Event;

class EventCollection extends Collection
{
    public static function createFromArray(array $data): EventCollection
    {
        $events = [];

        foreach ($data as $item) {
            $events[] = new Event($item);
        }

        return new self($events);
    }
}
