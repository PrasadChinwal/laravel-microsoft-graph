<?php

namespace PrasadChinwal\MicrosoftGraph\Endpoints;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PrasadChinwal\MicrosoftGraph\MicrosoftGraph;
use PrasadChinwal\MicrosoftGraph\Response\GraphUser;

class User extends MicrosoftGraph
{
    /**
     * @var string Base endpoint to graph users
     */
    protected string $endpoint = 'https://graph.microsoft.com/v1.0/users';

    /**
     * @throws RequestException
     */
    public function get(string $email = null): Collection
    {
        $url = $this->endpoint;
        if (Str::length($email) > 0) {
            $url = $url.'/'.$email;
        }

        return Http::withToken($this->getAccessToken())
            ->get($url)
            ->throwUnlessStatus(200)
            ->collect();
    }

    /**
     * @throws RequestException
     */
    public function find(string $email): Collection
    {
        return $this->get($email);
    }
}
