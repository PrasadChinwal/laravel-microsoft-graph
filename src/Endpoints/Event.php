<?php

namespace PrasadChinwal\MicrosoftGraph\Endpoints;

use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use PrasadChinwal\MicrosoftGraph\MicrosoftGraph;
use PrasadChinwal\MicrosoftGraph\Traits\HasQueryFilters;

class Event extends MicrosoftGraph
{
    use HasQueryFilters;

    protected string $email;

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
     * Get events for the user with applied filters.
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
            ->get('https://graph.microsoft.com/v1.0/users/{user_id}/events', [
                '$filter' => $this->filter,
            ])
            ->throwUnlessStatus(200)
            ->collect('value');

        return \PrasadChinwal\MicrosoftGraph\Response\Events\Event::collect($data);
    }

    /**
     * Create a new event from a Mailable instance.
     *
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
    public function cancel(string $eventId, ?string $message = null): Collection
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
    public function accept(string $eventId, ?string $message = null): Collection
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
    public function decline(string $eventId, ?string $message = null): Collection
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
     * @throws RequestException
     */
    public function update(string $eventId, Mailable $mailable): Collection
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
