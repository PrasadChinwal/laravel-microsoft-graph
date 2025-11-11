<?php

namespace PrasadChinwal\MicrosoftGraph\Response\Attachments;

class ItemAttachment extends Attachment
{
    public function __construct(
        public ?string $id,
        public ?string $contentType,
        public ?bool $isInline,
        public ?string $lastModifiedDateTime,
        public ?string $name,
        public ?int $size,
        public mixed $item,
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
