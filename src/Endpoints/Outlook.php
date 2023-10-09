<?php

namespace PrasadChinwal\MicrosoftGraph\Endpoints;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use PrasadChinwal\MicrosoftGraph\MicrosoftGraph;

class Outlook extends MicrosoftGraph
{
    protected string $enpoint = 'https://graph.microsoft.com/v1.0/';

    /**
     * @return \Illuminate\Support\Collection
     *
     * @throws RequestException
     */
    public function sendEmail(string $subject, string $message, array|string $to): \Illuminate\Support\Collection
    {
        return Http::withToken($this->getAccessToken())
            ->asJson()
            ->post('https://graph.microsoft.com/v1.0/user/1314e006-65ce-476a-86c1-ea8e1ddbd2db/sendMail', [
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
