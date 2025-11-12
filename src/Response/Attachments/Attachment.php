<?php

namespace PrasadChinwal\MicrosoftGraph\Response\Attachments;

use Spatie\LaravelData\Data;

class Attachment extends Data
{
    public function __construct(
        public ?string $id,
        public ?string $lastModifiedDateTime,
        public ?string $name,
        public ?string $contentType,
        public ?int $size,
        public ?bool $isInline,
        public ?string $contentId,
        public ?string $contentBytes,
    ) {}
}
