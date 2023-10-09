<?php

namespace PrasadChinwal\MicrosoftGraph\Response;

class Calendar
{
    public string $id;

    public string $name;

    public string $color;

    public string $hexColor;

    public string $isDefaultCalendar;

    public string $changeKey;

    public bool $canShare;

    public bool $canViewPrivateItems;

    public bool $canEdit;

    public array $allowedOnlineMeetingProviders;

    public string $defaultOnlineMeetingProvider;

    public bool $isTallyingResponses;

    public bool $isRemovable;

    public array $owner;

    public function __construct(array $items = [])
    {
        foreach ($items as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
