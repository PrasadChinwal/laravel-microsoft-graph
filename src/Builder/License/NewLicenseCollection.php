<?php

namespace PrasadChinwal\MicrosoftGraph\Builder\License;

use Illuminate\Support\Collection;

class NewLicenseCollection extends Collection
{
    public Collection $addLicenses;

    public function getAddLicenses(): array
    {
        return $this->addLicenses->toArray();
    }
}
