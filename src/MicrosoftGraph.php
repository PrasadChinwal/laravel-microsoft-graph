<?php

namespace PrasadChinwal\MicrosoftGraph;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PrasadChinwal\MicrosoftGraph\Endpoints\Attachment;
use PrasadChinwal\MicrosoftGraph\Endpoints\Calendar;
use PrasadChinwal\MicrosoftGraph\Endpoints\Event;
use PrasadChinwal\MicrosoftGraph\Endpoints\Mail;
use PrasadChinwal\MicrosoftGraph\Endpoints\Outlook;
use PrasadChinwal\MicrosoftGraph\Endpoints\User;

class MicrosoftGraph
{
    /**
     * Cache key for storing the access token.
     */
    protected const CACHE_KEY = 'microsoft_graph_access_token';

    /**
     * Cache key for storing the token expiry timestamp.
     */
    protected const CACHE_EXPIRY_KEY = 'microsoft_graph_token_expiry';

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
        $this->accessToken = $this->getOrRefreshAccessToken();
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
     * Get cached access token or refresh if expired.
     *
     * @throws RequestException
     */
    protected function getOrRefreshAccessToken(): string
    {
        // Check if token exists and is still valid
        $cachedToken = Cache::get(self::CACHE_KEY);
        $expiryTime = Cache::get(self::CACHE_EXPIRY_KEY);

        if ($cachedToken && $expiryTime && now()->timestamp < $expiryTime) {
            return $cachedToken;
        }

        // Token expired or doesn't exist, fetch new one
        $tokenData = $this->fetchAccessTokenFromApi();

        return $tokenData['access_token'];
    }

    /**
     * Fetch a fresh access token from the Microsoft API.
     *
     * @throws RequestException
     */
    protected function fetchAccessTokenFromApi(): array
    {
        $response = Http::asForm()
            ->post($this->getTokenUrl(), $this->getTokenFields())
            ->throwUnlessStatus(200)
            ->json();

        $accessToken = $response['access_token'];
        $expiresIn = $response['expires_in'] ?? 3600;

        // Cache token with 5 minute buffer before actual expiry
        $cacheUntil = now()->addSeconds($expiresIn - 300);
        $expiryTimestamp = now()->addSeconds($expiresIn)->timestamp;

        Cache::put(self::CACHE_KEY, $accessToken, $cacheUntil);
        Cache::put(self::CACHE_EXPIRY_KEY, $expiryTimestamp, $cacheUntil);

        return [
            'access_token' => $accessToken,
            'expires_in' => $expiresIn,
        ];
    }

    /**
     * Get the access token response for the given code.
     *
     * @deprecated Use getOrRefreshAccessToken() instead
     * @throws RequestException
     */
    public function getAccessTokenResponse(): Collection
    {
        $tokenData = $this->fetchAccessTokenFromApi();

        return collect($tokenData);
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
     * Get the access token.
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * Clear the cached access token.
     * Useful for testing or forcing token refresh.
     */
    public function clearTokenCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget(self::CACHE_EXPIRY_KEY);
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

    public function event(): Event
    {
        return new Event;
    }

    public function mail(): Mail
    {
        return new Mail;
    }

    public function attachments(): Attachment
    {
        return new Attachment;
    }
}
