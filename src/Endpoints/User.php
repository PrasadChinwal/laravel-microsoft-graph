<?php

namespace PrasadChinwal\MicrosoftGraph\Endpoints;

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
     * @return array|\Illuminate\Contracts\Pagination\CursorPaginator|\Illuminate\Contracts\Pagination\Paginator|\Illuminate\Pagination\AbstractCursorPaginator|\Illuminate\Pagination\AbstractPaginator|\Illuminate\Support\Collection|\Illuminate\Support\Enumerable|\Illuminate\Support\LazyCollection|\Spatie\LaravelData\CursorPaginatedDataCollection|\Spatie\LaravelData\DataCollection|\Spatie\LaravelData\PaginatedDataCollection
     */
    public function get(): array|\Illuminate\Contracts\Pagination\CursorPaginator|\Illuminate\Contracts\Pagination\Paginator|\Illuminate\Pagination\AbstractCursorPaginator|\Illuminate\Pagination\AbstractPaginator|\Illuminate\Support\Collection|\Illuminate\Support\Enumerable|\Illuminate\Support\LazyCollection|\Spatie\LaravelData\CursorPaginatedDataCollection|\Spatie\LaravelData\DataCollection|\Spatie\LaravelData\PaginatedDataCollection
    {
        $response = Http::withToken($this->getAccessToken())
            ->get($this->endpoint)
            ->throwUnlessStatus(200)
            ->collect('value');

        return \PrasadChinwal\MicrosoftGraph\Response\User\User::collect($response);
    }

    /**
     *
     * @throws \Exception
     */
    public function find(string $email): \PrasadChinwal\MicrosoftGraph\Response\User\User
    {
        if (Str::length($email) == 0) {
            throw new \Exception('Email address cannot be empty!');
        }

        $response = Http::withToken($this->getAccessToken())
            ->get( $this->endpoint . '/'.$email)
            ->throwUnlessStatus(200)
            ->collect();

        return \PrasadChinwal\MicrosoftGraph\Response\User\User::from($response);
    }

    /**
     * @throws \Exception
     */
    public function update(string $email, \PrasadChinwal\MicrosoftGraph\Builder\User\User $user): bool
    {
        if (Str::length($email) == 0) {
            throw new \Exception('Email address cannot be empty!');
        }

        $user = array_filter((array)$user);

        $response = Http::withToken($this->getAccessToken())
            ->patch( $this->endpoint . '/'.$email, $user)
            ->throwUnlessStatus(204);
        if ($response->status() == 204) {
            return true;
        }
        return false;
    }
}
