<?php

namespace PrasadChinwal\MicrosoftGraph\Calendar\Event\Eventable;

class Recurrence
{
    public RecurrencePattern $pattern;

    public RecurrenceRange $range;

    public function __construct(
       $pattern, $range
    )
    {
        $this->pattern = $pattern;
        $this->range = $range;
    }

}
