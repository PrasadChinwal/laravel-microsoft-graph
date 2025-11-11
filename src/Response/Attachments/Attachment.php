<?php

namespace PrasadChinwal\MicrosoftGraph\Response\Attachments;

use Spatie\LaravelData\Data;

class Attachment extends Data
{
    public function __construct(
        public ?string $id,
        public ?string $contentType,
        public ?bool $isInline,
        public ?string $lastModifiedDateTime,
        public ?string $name,
        public ?int $size,
    ) {}
}
