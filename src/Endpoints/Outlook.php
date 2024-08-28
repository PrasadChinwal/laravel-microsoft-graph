<?php

namespace PrasadChinwal\MicrosoftGraph\Endpoints;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use PrasadChinwal\MicrosoftGraph\MicrosoftGraph;

class Outlook extends MicrosoftGraph
{
    protected string $endpoint = 'https://graph.microsoft.com/v1.0/';

    /**
     * @param string $subject
     * @param string $message
     * @param array|string $to
     * @return Collection
     *
     * @throws RequestException
     */
    public function sendEmail(string $subject, string $message, array|string $to): \Illuminate\Support\Collection
    {
        return Http::graph()
            ->withToken($this->getAccessToken())
            ->asJson()
            ->post("$this->endpoint/user/1314e006-65ce-476a-86c1-ea8e1ddbd2db/sendMail", [
                'message' => [
                    'subject' => $subject,
                    'body' => json_encode([
                        'contentType' => 'text',
                        'content' => $message,
                    ]),
                    'toRecipients' => json_encode([
                        'emailAddress' => [
                            'address' => $to,
                        ],
                    ]),
                ],
            ])
            ->throwUnlessStatus(202)
            ->collect();
    }
}
