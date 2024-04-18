<?php

namespace PrasadChinwal\MicrosoftGraph\Calendar\Event\Eventable\Enums;

enum RecurrenceType: string
{
    CONST DAILY = 'daily';

    CONST WEEKLY = 'weekly';

    CONST ABSOLUTE_MONTHLY = 'absoluteMonthly';

    CONST RELATIVE_MONTHLY = 'relativeMonthly';

    CONST ABSOLUTE_YEARLY = 'absoluteYearly';

    CONST RELATIVE_YEARLY = 'relativeYearly';

}
