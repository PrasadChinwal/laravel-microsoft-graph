<?php

namespace PrasadChinwal\MicrosoftGraph\Endpoints;

use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use PrasadChinwal\MicrosoftGraph\Collections\CalendarCollection;
use PrasadChinwal\MicrosoftGraph\MicrosoftGraph;
use PrasadChinwal\MicrosoftGraph\Traits\HasQueryFilters;

class Calendar extends MicrosoftGraph
{
    use HasQueryFilters;

    protected string $email;

    /**
     * @var string Base endpoint to graph users
     */
    protected string $endpoint = 'https://graph.microsoft.com/v1.0/users';

    /**
     * Set the email address for the user.
     *
     * @return $this
     */
    public function for(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @throws RequestException
     */
    public function get(): Collection
    {
        $data = Http::graph()
            ->withToken($this->getAccessToken())
            ->withUrlParameters([
                'user_id' => $this->email,
            ])
            ->get('https://graph.microsoft.com/v1.0/users/{user_id}/calendars')
            ->throwUnlessStatus(200)
            ->collect()
            ->get('value');

        return CalendarCollection::createFromArray($data);
    }

    /**
     * @throws RequestException
     */
    public function schedule(array $users, Carbon $from, Carbon $to, string $timezone = 'UTC', int $interval = 30): Collection
    {
        return Http::graph()
            ->withToken($this->getAccessToken())
            ->withUrlParameters([
                'user_id' => $this->email,
            ])
            ->post('https://graph.microsoft.com/v1.0/users/{user_id}/calendar/getSchedule', [
                'schedules' => $users,
                'startTime' => [
                    'dateTime' => $from->format(DateTime::ATOM),
                    'timeZone' => $timezone,
                ],
                'endTime' => [
                    'dateTime' => $to->format(DateTime::ATOM),
                    'timeZone' => $timezone,
                ],
                'availabilityViewInterval' => $interval,
            ])
            ->throwUnlessStatus(200)
            ->collect();
    }

    /**
     * GET the calendar view for a specific period
     *
     * @param  string  $start  The start date and time of the calendar view
     * @param  string  $end  The end date and time of the calendar view
     * @return Collection The collection of calendar events for the specified period
     *
     * @throws RequestException If there is an error in the request
     */
    public function view(string $start, string $end)
    {
        $data = Http::graph()
            ->withToken($this->getAccessToken())
            ->withUrlParameters([
                'user_id' => $this->email,
            ])
            ->get('https://graph.microsoft.com/v1.0/users/{user_id}/calendar/calendarView', [
                'startDateTime' => $start,
                'endDateTime' => $end,
            ])
            ->throwUnlessStatus(200)
            ->collect('value');

        return \PrasadChinwal\MicrosoftGraph\Response\Events\Event::collect($data);
    }
}
