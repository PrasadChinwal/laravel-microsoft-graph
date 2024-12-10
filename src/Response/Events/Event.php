<?php

namespace PrasadChinwal\MicrosoftGraph\Response\Events;

use PrasadChinwal\MicrosoftGraph\Response\Events\Attendees\Attendee;
use PrasadChinwal\MicrosoftGraph\Response\Events\Attendees\AttendeeCollection;
use PrasadChinwal\MicrosoftGraph\Response\Events\Recurrence\Recurrence;
use Spatie\LaravelData\Data;

class Event extends Data
{
    public function __construct(
        public string $id,
        public string $subject,
        public ?string $bodyPreview,
        public ?string $createdDateTime,
        public ?string $lastModifiedDateTime,
        public ?string $changeKey,
        public array $categories,
        public ?string $transactionId,
        public ?string $originalStartTimeZone,
        public ?string $originalEndTimeZone,
        public ?string $iCalUId,
        public ?int $reminderMinutesBeforeStart,
        public ?bool $isReminderOn,
        public ?bool $hasAttachments,
        public ?string $importance,
        public ?string $sensitivity,
        public ?bool $isAllDay,
        public ?bool $isCancelled,
        public ?bool $isOrganizer,
        public ?bool $responseRequested,
        public ?string $seriesMasterId,
        public ?string $showAs,
        public ?string $type,
        public ?string $webLink,
        public ?string $onlineMeetingUrl,
        public ?bool $isOnlineMeeting,
        public ?string $onlineMeetingProvider,
        public ?bool $allowNewTimeProposals,
        public ?string $occurrenceId,
        public ?bool $isDraft,
        public ?bool $hideAttendees,
        public ?ResponseStatus $responseStatus,
        public ?EventBody $body,
        public ?EventTime $start,
        public ?EventTime $end,
        public ?array $location,
        /** @var LocationCollection<int, Location> */
        public ?array $locations,
        public ?Recurrence $recurrence,
        /** @var AttendeeCollection<int, Attendee> */
        public ?AttendeeCollection $attendees,
        public ?Organizer $organizer,
        public ?OnlineMeeting $onlineMeeting,
        public ?string $uid,
    ) {}
}