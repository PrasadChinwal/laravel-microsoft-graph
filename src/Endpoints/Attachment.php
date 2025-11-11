<?php

namespace PrasadChinwal\MicrosoftGraph\Endpoints;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use PrasadChinwal\MicrosoftGraph\Builder\Attachment\FileAttachmentBuilder;
use PrasadChinwal\MicrosoftGraph\Builder\Attachment\ReferenceAttachmentBuilder;
use PrasadChinwal\MicrosoftGraph\Exceptions\InvalidEmailException;
use PrasadChinwal\MicrosoftGraph\MicrosoftGraph;
use PrasadChinwal\MicrosoftGraph\Response\Attachments\Attachment as AttachmentResponse;

class Attachment extends MicrosoftGraph
{
    /**
     * The user's email address.
     */
    protected string $email;

    /**
     * The parent resource type (message or event).
     */
    protected string $resourceType;

    /**
     * The parent resource ID (message ID or event ID).
     */
    protected string $resourceId;

    /**
     * Base endpoint for Graph API.
     */
    protected string $baseEndpoint = 'https://graph.microsoft.com/v1.0/users';

    /**
     * Set context for message attachments.
     *
     * @param  string  $email  The user's email address
     * @param  string  $messageId  The message ID
     * @return $this
     *
     * @throws InvalidEmailException
     */
    public function forMessage(string $email, string $messageId): static
    {
        $this->validateEmail($email);
        $this->email = $email;
        $this->resourceType = 'messages';
        $this->resourceId = $messageId;

        return $this;
    }

    /**
     * Set context for event attachments.
     *
     * @param  string  $email  The user's email address
     * @param  string  $eventId  The event ID
     * @return $this
     *
     * @throws InvalidEmailException
     */
    public function forEvent(string $email, string $eventId): static
    {
        $this->validateEmail($email);
        $this->email = $email;
        $this->resourceType = 'events';
        $this->resourceId = $eventId;

        return $this;
    }

    /**
     * Get all attachments for the resource.
     *
     * @return Collection Collection of attachment objects
     *
     * @throws RequestException
     */
    public function list(): Collection
    {
        $this->ensureContextSet();

        $url = $this->buildResourceUrl().'/attachments';

        $response = Http::graph()
            ->withToken($this->getAccessToken())
            ->get($url)
            ->throwUnlessStatus(200)
            ->collect('value');

        return AttachmentResponse::collect($response);
    }

    /**
     * Get a specific attachment by ID.
     *
     * @param  string  $attachmentId  The attachment ID
     * @return AttachmentResponse
     *
     * @throws RequestException
     */
    public function get(string $attachmentId): AttachmentResponse
    {
        $this->ensureContextSet();

        $url = $this->buildResourceUrl()."/attachments/{$attachmentId}";

        $response = Http::graph()
            ->withToken($this->getAccessToken())
            ->get($url)
            ->throwUnlessStatus(200)
            ->collect();

        return AttachmentResponse::from($response);
    }

    /**
     * Get the raw content of a file attachment.
     *
     * @param  string  $attachmentId  The attachment ID
     * @return Response The HTTP response containing the raw file content
     *
     * @throws RequestException
     */
    public function getRaw(string $attachmentId): Response
    {
        $this->ensureContextSet();

        $url = $this->buildResourceUrl()."/attachments/{$attachmentId}/\$value";

        return Http::graph()
            ->withToken($this->getAccessToken())
            ->get($url)
            ->throwUnlessStatus(200);
    }

    /**
     * Add a file attachment to the resource.
     *
     * @param  FileAttachmentBuilder  $builder  The file attachment builder
     * @return AttachmentResponse
     *
     * @throws RequestException
     */
    public function addFile(FileAttachmentBuilder $builder): AttachmentResponse
    {
        $this->ensureContextSet();

        $url = $this->buildResourceUrl().'/attachments';

        $response = Http::graph()
            ->withToken($this->getAccessToken())
            ->asJson()
            ->post($url, $builder->toArray())
            ->throwUnlessStatus(201)
            ->collect();

        return AttachmentResponse::from($response);
    }

    /**
     * Add a reference attachment to the resource.
     *
     * @param  ReferenceAttachmentBuilder  $builder  The reference attachment builder
     * @return AttachmentResponse
     *
     * @throws RequestException
     */
    public function addReference(ReferenceAttachmentBuilder $builder): AttachmentResponse
    {
        $this->ensureContextSet();

        $url = $this->buildResourceUrl().'/attachments';

        $response = Http::graph()
            ->withToken($this->getAccessToken())
            ->asJson()
            ->post($url, $builder->toArray())
            ->throwUnlessStatus(201)
            ->collect();

        return AttachmentResponse::from($response);
    }

    /**
     * Add an attachment using raw array data.
     *
     * @param  array  $attachmentData  The attachment data
     * @return AttachmentResponse
     *
     * @throws RequestException
     */
    public function create(array $attachmentData): AttachmentResponse
    {
        $this->ensureContextSet();

        $url = $this->buildResourceUrl().'/attachments';

        $response = Http::graph()
            ->withToken($this->getAccessToken())
            ->asJson()
            ->post($url, $attachmentData)
            ->throwUnlessStatus(201)
            ->collect();

        return AttachmentResponse::from($response);
    }

    /**
     * Delete an attachment.
     *
     * @param  string  $attachmentId  The attachment ID
     * @return bool Returns true if successfully deleted
     *
     * @throws RequestException
     */
    public function delete(string $attachmentId): bool
    {
        $this->ensureContextSet();

        $url = $this->buildResourceUrl()."/attachments/{$attachmentId}";

        Http::graph()
            ->withToken($this->getAccessToken())
            ->delete($url)
            ->throwUnlessStatus(204);

        return true;
    }

    /**
     * Build the resource URL based on context.
     *
     * @return string
     */
    protected function buildResourceUrl(): string
    {
        return "{$this->baseEndpoint}/{$this->email}/{$this->resourceType}/{$this->resourceId}";
    }

    /**
     * Ensure the context (email, resource type, resource ID) is set.
     *
     * @throws \RuntimeException
     */
    protected function ensureContextSet(): void
    {
        if (! isset($this->email, $this->resourceType, $this->resourceId)) {
            throw new \RuntimeException(
                'Context not set. Call forMessage() or forEvent() before performing operations.'
            );
        }
    }

    /**
     * Validate email address.
     *
     * @throws InvalidEmailException
     */
    protected function validateEmail(string $email): void
    {
        if (empty($email)) {
            throw InvalidEmailException::empty();
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw InvalidEmailException::invalidFormat($email);
        }
    }
}
