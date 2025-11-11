<?php

namespace PrasadChinwal\MicrosoftGraph\Response\Attachments;

class FileAttachment extends Attachment
{
    public function __construct(
        public ?string $id,
        public ?string $contentType,
        public ?bool $isInline,
        public ?string $lastModifiedDateTime,
        public ?string $name,
        public ?int $size,
        public ?string $contentBytes,
        public ?string $contentId,
        public ?string $contentLocation,
    ) {
        parent::__construct(
            id: $id,
            contentType: $contentType,
            isInline: $isInline,
            lastModifiedDateTime: $lastModifiedDateTime,
            name: $name,
            size: $size
        );
    }
}
