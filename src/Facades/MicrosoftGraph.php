<?php

namespace PrasadChinwal\MicrosoftGraph\Facades;

use Illuminate\Support\Facades\Facade;

class MicrosoftGraph extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'graph';
    }
}
