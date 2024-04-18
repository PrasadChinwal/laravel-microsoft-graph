<?php

namespace PrasadChinwal\MicrosoftGraph\Endpoints;

use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use PrasadChinwal\MicrosoftGraph\Collections\EventCollection;
use PrasadChinwal\MicrosoftGraph\MicrosoftGraph;

class Event extends MicrosoftGraph
{
    protected string $email;

    protected ?string $filter = null;

    /**
     * @return $this
     */
    public function for(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @param $field
     * @param $condition
     * @param $value
     * @return $this
     */
    public function where($field, $condition, $value): static
    {
        $this->filter = Str::of($this->filter)
            ->whenNotEmpty(function (Stringable $string) {
                return $string->append(' and ');
            })
            ->append($field)
            ->append(' ')
            ->append($condition)
            ->append(' ')
            ->append(Str::wrap($value, "'"))
            ->value();
        return $this;
    }

    /**
     * @param $field
     * @param $condition
     * @param $value
     * @return $this
     */
    public function orWhere($field, $condition, $value): static
    {
        $this->filter = Str::of($this->filter)
            ->whenNotEmpty(function (Stringable $string) {
                return $string->append(' or ');
            })
            ->append($field)
            ->append(' ')
            ->append($condition)
            ->append(' ')
            ->append(Str::wrap($value, "'"))
            ->value();
        return $this;
    }

    /**
     * @return \Illuminate\Support\Collection
     *
     * @throws RequestException
     */
    public function get(): Collection
    {
        $data = Http::graph()
            ->withToken($this->getAccessToken())
            ->withUrlParameters([
                'user_id' => $this->email,
            ])
            ->get('https://graph.microsoft.com/v1.0/users/{user_id}/events',[
                '$filter' => $this->filter
            ])
            ->throwUnlessStatus(200)
            ->collect()
            ->get('value');
        return EventCollection::createFromArray($data);
    }

    // 300 S 9 th street

    /**
     * @throws RequestException
     */
    public function create(Mailable $mailable): Collection
    {
        $mailable->buildEnvelope();

        return Http::graph()
            ->withToken($this->getAccessToken())
            ->withUrlParameters([
                'user_id' => $mailable->envelope()->from->address,
            ])
            ->asJson()
            ->post('https://graph.microsoft.com/v1.0/users/{user_id}/events', [
                'subject' => $mailable->envelope()->subject,
                'body' => [
                    'contentType' => 'HTML',
                    'content' => $mailable->getEmailContent(),
                ],
                'start' => $mailable->envelope()->start,
                'end' => $mailable->envelope()->end,
                'location' => [
                    'displayName' => $mailable->envelope()->location,
                ],
                'attendees' => $mailable->envelope()->attendees,
                'isReminderOn' => $mailable->envelope()->reminder,
                'isOnlineMeeting' => $mailable->envelope()->isOnlineMeeting,
                'importance' => $mailable->envelope()->importance,
                'onlineMeetingProvider' => $mailable->envelope()->meetingProvider,
                'organizer' => [
                    'emailAddress' => [
                        '@odata.type' => 'microsoft.graph.emailAddress',
                    ],
                ],
                'recurrence' => $mailable->envelope()->recurrence ?? null,
            ])
            ->throwUnlessStatus(201)
            ->collect();
    }

    /**
     * @throws RequestException
     */
    public function cancel(string $eventId, string $message = null): Collection
    {
        return Http::graph()
            ->withToken($this->getAccessToken())
            ->withUrlParameters([
                'user_id' => $this->email,
                'event_id' => $eventId,
            ])
            ->asJson()
            ->post('https://graph.microsoft.com/v1.0/users/{user_id}/events/{event_id}/cancel', [
                'comment' => $message,
            ])
            ->throwUnlessStatus(202)
            ->collect();
    }

    /**
     * @throws RequestException
     */
    public function accept(string $eventId, string $message = null): Collection
    {
        return Http::graph()
            ->withToken($this->getAccessToken())
            ->withUrlParameters([
                'user_id' => $this->email,
                'event_id' => $eventId,
            ])
            ->asJson()
            ->post('https://graph.microsoft.com/v1.0/users/{user_id}/events/{event_id}/accept', [
                'comment' => $message,
            ])
            ->throwUnlessStatus(200)
            ->collect();
    }

    /**
     * @throws RequestException
     */
    public function decline(string $eventId, string $message = null): Collection
    {
        return Http::graph()
            ->withToken($this->getAccessToken())
            ->withUrlParameters([
                'user_id' => $this->email,
                'event_id' => $eventId,
            ])
            ->asJson()
            ->post('https://graph.microsoft.com/v1.0/users/{user_id}/events/{event_id}/decline', [
                'comment' => $message,
            ])
            ->throwUnlessStatus(200)
            ->collect();
    }

    /**
     * @param  string  $eventId
     * @param  Mailable  $mailable
     * @return Collection
     * @throws RequestException
     */
    public function update(string $eventId, Mailable $mailable,): Collection
    {
        return Http::graph()
            ->withToken($this->getAccessToken())
            ->withUrlParameters([
                'user_id' => $mailable->envelope()->from->address,
                'event_id' => $eventId,
            ])
            ->asJson()
            ->patch(
                'https://graph.microsoft.com/v1.0/users/{user_id}/events/{event_id}',
                $mailable->buildEnvelope()
            )
            ->throwUnlessStatus(200)
            ->collect();
    }
}
