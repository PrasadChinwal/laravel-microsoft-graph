<?php

namespace PrasadChinwal\MicrosoftGraph\Endpoints;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PrasadChinwal\MicrosoftGraph\Contracts\HasLicenses;
use PrasadChinwal\MicrosoftGraph\Contracts\HasProfilePhoto;
use PrasadChinwal\MicrosoftGraph\MicrosoftGraph;
use PrasadChinwal\MicrosoftGraph\Traits\HasProfilePhoto as ProfilePhoto;
use PrasadChinwal\MicrosoftGraph\Traits\LicenseDetails;

class User extends MicrosoftGraph implements HasLicenses, HasProfilePhoto
{
    use LicenseDetails;
    use ProfilePhoto;

    /**
     * @var string Base endpoint to graph users
     */
    protected string $endpoint = 'https://graph.microsoft.com/v1.0/users';

    private string $email = '';

    public function withEmail(string $email): User
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @throws RequestException
     */
    public function get(?string $email = null): \PrasadChinwal\MicrosoftGraph\Response\User\User
    {
        $url = $this->endpoint;
        if (Str::length($email) > 0) {
            $url = $url.'/'.$email;
        }

        $response = Http::withToken($this->getAccessToken())
            ->get($url)
            ->throwUnlessStatus(200)
            ->collect();

        return \PrasadChinwal\MicrosoftGraph\Response\User\User::from($response);
    }

    /**
     * @throws RequestException
     */
    public function find(string $email): \PrasadChinwal\MicrosoftGraph\Response\User\User
    {
        return $this->get($email);
    }
}
