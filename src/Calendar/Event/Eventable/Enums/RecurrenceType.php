<?php

namespace PrasadChinwal\MicrosoftGraph\Calendar\Event\Eventable\Enums;

enum RecurrenceType: string
{
    const DAILY = 'daily';

    const WEEKLY = 'weekly';

    const ABSOLUTE_MONTHLY = 'absoluteMonthly';

    const RELATIVE_MONTHLY = 'relativeMonthly';

    const ABSOLUTE_YEARLY = 'absoluteYearly';

    const RELATIVE_YEARLY = 'relativeYearly';
}
