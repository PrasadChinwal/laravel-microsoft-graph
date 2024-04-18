<?php

namespace PrasadChinwal\MicrosoftGraph\Contracts;

interface HasProfilePhoto
{
    public function getPhoto();

    public function updatePhoto(string $image);

    public function deletePhoto();
}
