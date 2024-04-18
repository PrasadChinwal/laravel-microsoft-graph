<?php

namespace PrasadChinwal\MicrosoftGraph\Endpoints;

use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use PrasadChinwal\MicrosoftGraph\Collections\CalendarCollection;
use PrasadChinwal\MicrosoftGraph\MicrosoftGraph;

class Calendar extends MicrosoftGraph
{
    protected string $email;

    /**
     * @var string Base endpoint to graph users
     */
    protected string $endpoint = 'https://graph.microsoft.com/v1.0/users';

    /**
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
}
