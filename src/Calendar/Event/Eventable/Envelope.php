<?php

namespace PrasadChinwal\MicrosoftGraph\Calendar\Event\Eventable;

use Carbon\Carbon;
use DateTime;
use Illuminate\Mail\Mailables\Address;
use PrasadChinwal\MicrosoftGraph\Calendar\Event\Eventable\Enums\CalendarEventImportance;
use PrasadChinwal\MicrosoftGraph\Calendar\Event\Eventable\Enums\OnlineMeetingProvider;

class Envelope
{
    public Address $from;

    public string $subject;

    public array $attendees = [];

    public object $start;

    public object $end;

    public ?string $location;

    public bool $isOnlineMeeting = false;

    public CalendarEventImportance $importance;

    public bool $reminder = true;

    public OnlineMeetingProvider $meetingProvider = OnlineMeetingProvider::UNKNOWN;

    public string $timeZone;

    public ?Recurrence $recurrence;

    public array $to = [];

    public array $cc = [];

    public array $bcc = [];

    public array $replyTo = [];

    public array $tags = [];

    public array $metadata = [];

    public array $using = [];

    /**
     * Create new instance of calendar event envelope
     */
    public function __construct(
        Address|string $from, ?string $subject = null, Attendee|array $attendees = [],
        ?Carbon $start = null, ?Carbon $end = null, ?string $location = null,
        bool $isOnlineMeeting = false,
        CalendarEventImportance $importance = CalendarEventImportance::NORMAL,
        bool $reminder = true, OnlineMeetingProvider $meetingProvider = OnlineMeetingProvider::UNKNOWN,
        ?Recurrence $recurrence = null
    ) {
        $this->setTimeZone(null);
        $this->from = is_string($from) ? new Address($from) : $from;
        $this->subject = $subject;
        $this->start = $this->normalizeDates($start);
        $this->end = $this->normalizeDates($end);
        $this->location = $location;
        $this->attendees = $this->normalizeAttendees($attendees);
        $this->isOnlineMeeting = $isOnlineMeeting;
        $this->importance = $importance;
        $this->reminder = $reminder;
        $this->meetingProvider = $meetingProvider;
        $this->recurrence = $recurrence;
        $this->to = $attendees;
    }

    public function normalizeDates(Carbon $date): object
    {
        return (object) [
            'dateTime' => $date->format(DateTime::ATOM),
            'timeZone' => $this->timeZone,
        ];
    }

    public function normalizeAttendees(array|Attendee $attendees): array
    {
        return collect($attendees)->map(function ($attendee) {
            $attendee = is_string($attendee) ? new Attendee($attendee) : $attendee;

            return (object) [
                'emailAddress' => [
                    'address' => $attendee->email,
                    'name' => $attendee->name,
                ],
                'type' => $attendee->required ? 'required' : 'optional',
            ];
        })->all();
    }

    /**
     * @param  string  $email
     * @param  string  $name
     * @param  bool  $required
     * @return $this
     */
    //    public function attendees(string $email, string $name, bool $required): static
    //    {
    //        $this->attendees = array_merge($this->attendees, $this->normalizeAttendees(
    //            is_string($name) ? [new Attendee($email, $name)] : Arr::wrap($email),
    //        ));
    //        return $this;
    //    }

    /**
     * Get the time zone of the calendar event
     */
    public function getTimeZone(): string
    {
        return $this->timeZone;
    }

    /**
     * Set the time zone for the calendar event
     *
     * @param  string|null  $timeZone  The time zone to set
     */
    public function setTimeZone(?string $timeZone): void
    {
        if (! $timeZone) {
            $this->timeZone = config('app.timezone', 'UTC');
        } else {
            $this->timeZone = $timeZone;
        }
    }
}
