<?php

namespace PrasadChinwal\MicrosoftGraph;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use PrasadChinwal\MicrosoftGraph\Endpoints\Calendar;
use PrasadChinwal\MicrosoftGraph\Endpoints\Event;
use PrasadChinwal\MicrosoftGraph\Endpoints\Mail;
use PrasadChinwal\MicrosoftGraph\Endpoints\Outlook;
use PrasadChinwal\MicrosoftGraph\Endpoints\User;

class MicrosoftGraph
{
    /**
     * The HTTP Client instance.
     *
     * @var Http
     */
    protected $httpClient;

    /**
     * The custom parameters to be sent with the request.
     */
    protected array $parameters = [];

    /**
     * The scopes being requested.
     */
    protected array $scopes = [];

    /**
     * The separating character for the requested scopes.
     */
    protected string $scopeSeparator = ',';

    /**
     * The cached user instance.
     */
    protected $user;

    /**
     * Access Token for microsoft graph api
     */
    protected string $accessToken;

    /**
     * Create a new provider instance.
     *
     * @return void
     *
     * @throws RequestException
     */
    public function __construct()
    {
        $this->getAccessTokenResponse();
    }

    protected function getTenantId()
    {
        return config('microsoft-graph.tenant_id');
    }

    protected function getClientId()
    {
        return config('microsoft-graph.client_id');
    }

    protected function getClientSecret()
    {
        return config('microsoft-graph.client_secret');
    }

    /**
     * Get the token URL for the Token.
     */
    protected function getTokenUrl(): string
    {
        return "https://login.microsoftonline.com/{$this->getTenantId()}/oauth2/v2.0/token";
    }

    /**
     * Get the access token response for the given code.
     *
     * @throws RequestException
     */
    public function getAccessTokenResponse(): Collection
    {
        $response = Http::asForm()
            ->post($this->getTokenUrl(), $this->getTokenFields())
            ->throwUnlessStatus(200);
        $this->accessToken = $response->collect()->get('access_token');

        return $response->collect();
    }

    /**
     * Get the POST fields for the token request.
     */
    protected function getTokenFields(): array
    {
        return [
            'client_id' => $this->getClientId(),
            'client_secret' => $this->getClientSecret(),
            'grant_type' => 'client_credentials',
            'scope' => 'https://graph.microsoft.com/.default',
        ];
    }

    /**
     * Get the access token response for the given code.
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * Merge the scopes of the requested access.
     *
     * @param  array|string  $scopes
     * @return $this
     */
    public function scopes($scopes)
    {
        $this->scopes = array_unique(array_merge($this->scopes, (array) $scopes));

        return $this;
    }

    /**
     * Set the scopes of the requested access.
     *
     * @param  array|string  $scopes
     * @return $this
     */
    public function setScopes($scopes)
    {
        $this->scopes = array_unique((array) $scopes);

        return $this;
    }

    /**
     * Get the current scopes.
     *
     * @return array
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * Set the request instance.
     *
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Set the custom parameters of the request.
     *
     * @return $this
     */
    public function with(array $parameters): static
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function outlook(): Outlook
    {
        return new Outlook;
    }

    public function calendar(): Calendar
    {
        return new Calendar;
    }

    public function users(): User
    {
        return new User;
    }

    public static function event(): Event
    {
        return new Event;
    }

    public static function mail(): Mail
    {
        return new Mail;
    }
}
