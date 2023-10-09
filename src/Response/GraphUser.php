<?php

namespace PrasadChinwal\MicrosoftGraph\Response;

use Illuminate\Support\Collection;

class GraphUser extends Collection
{
    /**
     * Create a new currency instance.
     */
    public function __construct(public $user)
    {
        parent::__construct();
    }
}
