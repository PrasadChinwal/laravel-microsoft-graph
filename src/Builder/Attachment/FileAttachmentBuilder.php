<?php

namespace PrasadChinwal\MicrosoftGraph\Builder\Attachment;

class FileAttachmentBuilder
{
    /**
     * The attachment type.
     */
    public string $odataType = '#microsoft.graph.fileAttachment';

    /**
     * The name of the attachment file.
     */
    public string $name;

    /**
     * The binary contents of the file (base64 encoded).
     */
    public string $contentBytes;

    /**
     * The MIME type of the attachment.
     */
    public ?string $contentType = null;

    /**
     * The content ID for inline attachments.
     */
    public ?string $contentId = null;

    /**
     * Whether the attachment is inline.
     */
    public bool $isInline = false;

    /**
     * The size of the attachment in bytes.
     */
    public ?int $size = null;

    /**
     * Create a file attachment from a file path.
     */
    public static function fromFile(string $filePath, ?string $name = null): self
    {
        if (! file_exists($filePath)) {
            throw new \InvalidArgumentException("File not found: {$filePath}");
        }

        $fileSize = filesize($filePath);
        if ($fileSize > 3 * 1024 * 1024) {
            throw new \InvalidArgumentException('File size exceeds 3MB limit. Use upload session for larger files.');
        }

        $builder = new self();
        $builder->name = $name ?? basename($filePath);
        $builder->contentBytes = base64_encode(file_get_contents($filePath));
        $builder->contentType = mime_content_type($filePath) ?: 'application/octet-stream';
        $builder->size = $fileSize;

        return $builder;
    }

    /**
     * Create a file attachment from raw data.
     */
    public static function fromData(string $data, string $name, ?string $contentType = null): self
    {
        $dataSize = strlen($data);
        if ($dataSize > 3 * 1024 * 1024) {
            throw new \InvalidArgumentException('Data size exceeds 3MB limit. Use upload session for larger files.');
        }

        $builder = new self();
        $builder->name = $name;
        $builder->contentBytes = base64_encode($data);
        $builder->contentType = $contentType ?? 'application/octet-stream';
        $builder->size = $dataSize;

        return $builder;
    }

    /**
     * Set the attachment as inline.
     */
    public function asInline(string $contentId): self
    {
        $this->isInline = true;
        $this->contentId = $contentId;

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
            'contentBytes' => $this->contentBytes,
        ];

        if ($this->contentType) {
            $data['contentType'] = $this->contentType;
        }

        if ($this->isInline) {
            $data['isInline'] = true;
            if ($this->contentId) {
                $data['contentId'] = $this->contentId;
            }
        }

        return $data;
    }
}
