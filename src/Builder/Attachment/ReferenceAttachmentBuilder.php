<?php

namespace PrasadChinwal\MicrosoftGraph\Builder\Attachment;

class ReferenceAttachmentBuilder
{
    /**
     * The attachment type.
     */
    public string $odataType = '#microsoft.graph.referenceAttachment';

    /**
     * The name of the attachment.
     */
    public string $name;

    /**
     * The URL to the source file.
     */
    public string $sourceUrl;

    /**
     * The provider type (e.g., oneDriveBusiness, oneDriveConsumer, dropbox).
     */
    public ?string $providerType = null;

    /**
     * Whether the attachment is inline.
     */
    public bool $isInline = false;

    /**
     * The permission level (view, edit, anonymousView, anonymousEdit, organizationView, organizationEdit).
     */
    public ?string $permission = null;

    /**
     * URL to a thumbnail image.
     */
    public ?string $thumbnailUrl = null;

    /**
     * URL for previewing the file.
     */
    public ?string $previewUrl = null;

    /**
     * Whether the reference is a folder.
     */
    public bool $isFolder = false;

    /**
     * Create a reference attachment from a URL.
     */
    public static function fromUrl(string $url, string $name): self
    {
        $builder = new self();
        $builder->sourceUrl = $url;
        $builder->name = $name;

        return $builder;
    }

    /**
     * Set the provider type.
     */
    public function setProvider(string $providerType): self
    {
        $this->providerType = $providerType;

        return $this;
    }

    /**
     * Set the permission level.
     */
    public function setPermission(string $permission): self
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * Set the thumbnail URL.
     */
    public function setThumbnail(string $thumbnailUrl): self
    {
        $this->thumbnailUrl = $thumbnailUrl;

        return $this;
    }

    /**
     * Set the preview URL.
     */
    public function setPreview(string $previewUrl): self
    {
        $this->previewUrl = $previewUrl;

        return $this;
    }

    /**
     * Mark as a folder reference.
     */
    public function asFolder(): self
    {
        $this->isFolder = true;

        return $this;
    }

    /**
     * Convert to array for API request.
     */
    public function toArray(): array
    {
        $data = [
            '@odata.type' => $this->odataType,
            'name' => $this->name,
            'sourceUrl' => $this->sourceUrl,
        ];

        if ($this->providerType) {
            $data['providerType'] = $this->providerType;
        }

        if ($this->permission) {
            $data['permission'] = $this->permission;
        }

        if ($this->thumbnailUrl) {
            $data['thumbnailUrl'] = $this->thumbnailUrl;
        }

        if ($this->previewUrl) {
            $data['previewUrl'] = $this->previewUrl;
        }

        if ($this->isFolder) {
            $data['isFolder'] = true;
        }

        if ($this->isInline) {
            $data['isInline'] = true;
        }

        return $data;
    }
}
