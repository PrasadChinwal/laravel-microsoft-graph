<?php

namespace PrasadChinwal\MicrosoftGraph\Response;

class Event
{
    protected string $id;

    protected string $subject;

    protected $bodyPreview;

    protected $createdDateTime;

    protected $lastModifiedDateTime;

    protected string $changeKey;

    protected array $categories;

    protected string $transactionId;

    protected $originalStartTimeZone;

    protected $originalEndTimeZone;

    protected string $iCalUId;

    protected $reminderMinutesBeforeStart;

    protected bool $isReminderOn;

    protected bool $hasAttachments;

    protected string $importance;

    protected string $sensitivity;

    protected bool $isAllDay;

    protected bool $isCancelled;

    protected bool $isOrganizer;

    protected bool $responseRequested;

    protected $seriesMasterId;

    protected string $showAs;

    protected string $type;

    protected string $webLink;

    protected string $onlineMeetingUrl;

    protected bool $isOnlineMeeting;

    protected string $onlineMeetingProvider;

    protected bool $allowNewTimeProposals;

    protected $occurrenceId;

    protected bool $isDraft;

    protected bool $hideAttendees;

    protected array $responseStatus;

    protected array $body;

    protected array $start;

    protected array $end;

    protected array $location;

    protected array $locations;

    protected string $recurrence;

    protected array $attendees;

    protected array $organizer;

    protected string $onlineMeeting;

    public function __construct(array $items = [])
    {
        foreach ($items as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
