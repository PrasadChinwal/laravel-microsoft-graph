<?php

namespace PrasadChinwal\MicrosoftGraph\Endpoints;

use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use PrasadChinwal\MicrosoftGraph\Collections\EventCollection;
use PrasadChinwal\MicrosoftGraph\MicrosoftGraph;

class Event extends MicrosoftGraph
{
    protected string $email;

    /**
     * @return $this
     */
    public function for(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return \Illuminate\Support\Collection
     *
     * @throws RequestException
     */
    public function get(): Collection
    {
        return Http::withToken($this->getAccessToken())
            ->withUrlParameters([
                'user_id' => $this->email,
            ])
            ->get('https://graph.microsoft.com/v1.0/users/{user_id}/events')
            ->throwUnlessStatus(200)
            ->collect()
            ->mapInto(EventCollection::class);
    }

    /**
     * @throws RequestException
     */
    public function create(Mailable $mailable): Collection
    {
        $mailable->buildEnvelope();

        return Http::withToken($this->getAccessToken())
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
            ])
            ->throwUnlessStatus(201)
            ->collect();
    }

    /**
     * @throws RequestException
     */
    public function cancel(string $eventId, string $message = null): Collection
    {
        return Http::withToken($this->getAccessToken())
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
        return Http::withToken($this->getAccessToken())
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
        return Http::withToken($this->getAccessToken())
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
     * @throws RequestException
     */
    public function update(Mailable $mailable, string $eventId): Collection
    {
        return Http::withToken($this->getAccessToken())
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
