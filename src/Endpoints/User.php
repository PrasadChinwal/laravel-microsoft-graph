<?php

namespace PrasadChinwal\MicrosoftGraph\Endpoints;

use Illuminate\Support\Facades\Http;
use PrasadChinwal\MicrosoftGraph\Contracts\HasLicenses;
use PrasadChinwal\MicrosoftGraph\Contracts\HasProfilePhoto;
use PrasadChinwal\MicrosoftGraph\Exceptions\InvalidEmailException;
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
     * Find a specific user by email address.
     *
     * @throws InvalidEmailException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function find(string $email): \PrasadChinwal\MicrosoftGraph\Response\User\User
    {
        $this->validateEmail($email);

        $response = Http::withToken($this->getAccessToken())
            ->get($this->endpoint.'/'.$email)
            ->throwUnlessStatus(200)
            ->collect();

        return \PrasadChinwal\MicrosoftGraph\Response\User\User::from($response);
    }

    /**
     * Update a user's properties.
     *
     * @throws InvalidEmailException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function update(string $email, \PrasadChinwal\MicrosoftGraph\Builder\User\User $user): bool
    {
        $this->validateEmail($email);

        // Convert user object to array and filter out null values
        $userData = array_filter(get_object_vars($user), fn($value) => $value !== null);

        Http::withToken($this->getAccessToken())
            ->patch($this->endpoint.'/'.$email, $userData)
            ->throwUnlessStatus(204);

        return true;
    }

    /**
     * Validate email address.
     *
     * @throws InvalidEmailException
     */
    protected function validateEmail(string $email): void
    {
        if (empty($email)) {
            throw InvalidEmailException::empty();
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw InvalidEmailException::invalidFormat($email);
        }
    }
}
