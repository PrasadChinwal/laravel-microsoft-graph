<?php

namespace PrasadChinwal\MicrosoftGraph\Traits;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

trait HasProfilePhoto
{
    /**
     * Get the profile photo for the user.
     *
     * @param  string|null  $width  Optional width parameter (not currently used by API)
     * @param  string|null  $height  Optional height parameter (not currently used by API)
     * @return Response The response containing the photo binary data
     *
     * @throws RequestException
     */
    public function getPhoto(?string $width = null, ?string $height = null): Response
    {
        return Http::withToken($this->getAccessToken())
            ->withHeaders([
                'Content-Type' => 'image/jpg',
            ])
            ->get("{$this->endpoint}/{$this->email}/photo/\$value")
            ->throwUnlessStatus(200);
    }

    /**
     * Update the photo of a user.
     *
     * @param  string  $image  The binary image data
     * @return Response The response from the server upon successful photo update
     *
     * @throws RequestException
     */
    public function updatePhoto(string $image): Response
    {
        return Http::withToken($this->getAccessToken())
            ->withHeaders([
                'Content-Type' => 'image/jpg',
            ])
            ->withBody($image, 'image/jpeg')
            ->put("{$this->endpoint}/{$this->email}/photo/\$value")
            ->throwUnlessStatus(200);
    }

    /**
     * Delete the profile photo for the user.
     *
     * @return bool Returns true if the photo was successfully deleted
     *
     * @throws RequestException
     */
    public function deletePhoto(): bool
    {
        Http::withToken($this->getAccessToken())
            ->delete("{$this->endpoint}/{$this->email}/photo/\$value")
            ->throwUnlessStatus(204);

        return true;
    }
}
