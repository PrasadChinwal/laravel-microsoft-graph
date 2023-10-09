<?php

namespace PrasadChinwal\MicrosoftGraph\Calendar\Event\Eventable\Enums;

enum CalendarEventImportance: string
{
    case HIGH = 'high';
    case NORMAL = 'normal';
    case LOW = 'low';
}
