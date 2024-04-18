<?php

namespace PrasadChinwal\MicrosoftGraph\Traits;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

trait HasProfilePhoto
{
    /**
     * @throws RequestException
     */
    public function getPhoto(string $width = null, string $height = null)
    {
        return Http::withToken($this->getAccessToken())
            ->withHeaders([
                'Content-Type' => 'image/jpg'
            ])
            ->get("{$this->endpoint}/{$this->email}/photo/\$value")
            ->throwUnlessStatus(200);
    }

    /**
     * Updates the photo of a user.
     *
     * @param string $image The path to the new photo image file.
     * @return \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response The response from the server upon successful photo update.
     * @throws RequestException If there is an error while making the request to update the photo.
     */
    public function updatePhoto(string $image)
    {
        return Http::withToken($this->getAccessToken())
            ->withHeaders([
                'Content-Type' => 'image/jpg'
            ])
            ->withBody($image, 'image/jpeg')
            ->put("{$this->endpoint}/{$this->email}/photo/\$value")
            ->throwUnlessStatus(200);
    }

    public function deletePhoto()
    {

    }
}
