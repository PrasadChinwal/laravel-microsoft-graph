<?php

namespace PrasadChinwal\MicrosoftGraph\Traits;

use Illuminate\Support\Facades\Http;
use PrasadChinwal\MicrosoftGraph\Builder\License\AssignLicenseBuilder;
use PrasadChinwal\MicrosoftGraph\Response\LicenseDto\License;
use PrasadChinwal\MicrosoftGraph\Response\User\User;

trait LicenseDetails
{
    /**
     * Retrieve the licenses of a user.
     */
    public function getLicenses(): array|\Illuminate\Contracts\Pagination\CursorPaginator|\Illuminate\Contracts\Pagination\Paginator|\Illuminate\Pagination\AbstractCursorPaginator|\Illuminate\Pagination\AbstractPaginator|\Illuminate\Support\Collection|\Illuminate\Support\Enumerable|\Illuminate\Support\LazyCollection|\Spatie\LaravelData\CursorPaginatedDataCollection|\Spatie\LaravelData\DataCollection|\Spatie\LaravelData\PaginatedDataCollection
    {
        $response = Http::withToken($this->getAccessToken())
            ->get("{$this->endpoint}/{$this->email}/licenseDetails")
            ->throwUnlessStatus(200);

        return License::collect($response->collect('value'));
    }

    /**
     * Assign a license to a user.
     *
     * @param  AssignLicenseBuilder  $assignLicenseBuilder  An instance of AssignLicenseBuilder containing the license assignment details.
     * @return User The user object representing the user with the assigned license.
     */
    public function assignLicense(AssignLicenseBuilder $assignLicenseBuilder): User
    {
        $response = Http::withToken($this->getAccessToken())
            ->post("{$this->endpoint}/{$this->email}/assignLicense", $assignLicenseBuilder)
            ->throwUnlessStatus(200)
            ->collect();

        return User::from($response);
    }
}
