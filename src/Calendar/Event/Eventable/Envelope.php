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

    public string $location;

    public bool $isOnlineMeeting = false;

    public CalendarEventImportance $importance;

    public bool $reminder = true;

    public OnlineMeetingProvider $meetingProvider = OnlineMeetingProvider::UNKNOWN;

    public string $timeZone = 'UTC';

    /**
     * Create new instance of calendar event envelope
     */
    public function __construct(
        Address|string $from, string $subject = null, Attendee|array $attendees = [],
        Carbon $start = null, Carbon $end = null, string $location = null,
        bool $isOnlineMeeting = false,
        CalendarEventImportance $importance = CalendarEventImportance::NORMAL,
        bool $reminder = true, OnlineMeetingProvider $meetingProvider = OnlineMeetingProvider::UNKNOWN
    ) {
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
}
