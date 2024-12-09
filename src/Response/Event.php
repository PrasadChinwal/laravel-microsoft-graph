<?php

namespace PrasadChinwal\MicrosoftGraph\Response;

class Event
{
    public string $id;

    public string $subject;

    public $bodyPreview;

    public $createdDateTime;

    public $lastModifiedDateTime;

    public string $changeKey;

    public array $categories;

    public ?string $transactionId;

    public $originalStartTimeZone;

    public $originalEndTimeZone;

    public string $iCalUId;

    public $reminderMinutesBeforeStart;

    public bool $isReminderOn;

    public bool $hasAttachments;

    public string $importance;

    public string $sensitivity;

    public bool $isAllDay;

    public bool $isCancelled;

    public bool $isOrganizer;

    public bool $responseRequested;

    public $seriesMasterId;

    public string $showAs;

    public string $type;

    public string $webLink;

    public ?string $onlineMeetingUrl;

    public bool $isOnlineMeeting;

    public string $onlineMeetingProvider;

    public bool $allowNewTimeProposals;

    public $occurrenceId;

    public bool $isDraft;

    public bool $hideAttendees;

    public array $responseStatus;

    public array $body;

    public array $start;

    public array $end;

    public array $location;

    public array $locations;

    public ?array $recurrence;

    public array $attendees;

    public array $organizer;

    public ?array $onlineMeeting;

    public function __construct(array $items = [])
    {
        foreach ($items as $key => $value) {
            (object) $this->{$key} = $value;
        }
    }
}
