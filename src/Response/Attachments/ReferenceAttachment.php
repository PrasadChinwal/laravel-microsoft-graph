<?php

namespace PrasadChinwal\MicrosoftGraph\Response\Attachments;

class ReferenceAttachment extends Attachment
{
    public function __construct(
        public ?string $id,
        public ?string $contentType,
        public ?bool $isInline,
        public ?string $lastModifiedDateTime,
        public ?string $name,
        public ?int $size,
        public ?string $sourceUrl,
        public ?string $providerType,
        public ?string $thumbnailUrl,
        public ?string $previewUrl,
        public ?string $permission,
        public ?bool $isFolder,
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
