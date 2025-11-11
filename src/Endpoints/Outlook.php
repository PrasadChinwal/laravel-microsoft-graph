<?php

namespace PrasadChinwal\MicrosoftGraph\Endpoints;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use PrasadChinwal\MicrosoftGraph\MicrosoftGraph;

class Outlook extends MicrosoftGraph
{
    protected string $endpoint = 'https://graph.microsoft.com/v1.0/';

    protected string $email;

    /**
     * Set the email address for the user sending the email.
     *
     * @return $this
     */
    public function for(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Send an email on behalf of the specified user.
     *
     * @param  string  $subject  The email subject
     * @param  string  $message  The email message content
     * @param  array|string  $to  The recipient(s) email address(es)
     * @return \Illuminate\Support\Collection
     *
     * @throws RequestException
     */
    public function sendEmail(string $subject, string $message, array|string $to): \Illuminate\Support\Collection
    {
        // Normalize recipients to array format
        $recipients = is_array($to) ? $to : [$to];
        $toRecipients = array_map(fn($email) => [
            'emailAddress' => ['address' => $email]
        ], $recipients);

        return Http::graph()
            ->withToken($this->getAccessToken())
            ->withUrlParameters([
                'user_id' => $this->email,
            ])
            ->asJson()
            ->post('https://graph.microsoft.com/v1.0/users/{user_id}/sendMail', [
                'message' => [
                    'subject' => $subject,
                    'body' => [
                        'contentType' => 'text',
                        'content' => $message,
                    ],
                    'toRecipients' => $toRecipients,
                ],
            ])
            ->throwUnlessStatus(202)
            ->collect();
    }
}
