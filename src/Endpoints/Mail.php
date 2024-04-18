<?php

namespace PrasadChinwal\MicrosoftGraph\Endpoints;

use Illuminate\Support\Facades\Http;
use PrasadChinwal\MicrosoftGraph\MicrosoftGraph;

class Mail extends MicrosoftGraph
{
    protected string $email;

    /**
     * @param  string  $email
     * @return Mail
     */
    public function for(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return \Illuminate\Support\Collection
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function get()
    {
        return Http::graph()
            ->withToken($this->getAccessToken())
            ->withUrlParameters([
                'user_id' => $this->email,
            ])
            ->get('https://graph.microsoft.com/v1.0/users/{user_id}/messages')
            ->throwUnlessStatus(200)
            ->collect();
    }
}
