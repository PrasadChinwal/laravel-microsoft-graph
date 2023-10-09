<?php

namespace PrasadChinwal\MicrosoftGraph\Calendar\Event\Eventable\Enums;

enum OnlineMeetingProvider: string
{
    case UNKNOWN = 'unknown';
    case TEAMS_FOR_BUSINESS = 'teamsForBusiness';
    case SKYPE_FOR_BUSINESS = 'skypeForBusiness';
    case SKYPE_FOR_CONSUMER = 'skypeForConsumer';
}
