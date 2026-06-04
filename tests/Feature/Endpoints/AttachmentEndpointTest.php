<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PrasadChinwal\MicrosoftGraph\Builder\Attachment\FileAttachmentBuilder;
use PrasadChinwal\MicrosoftGraph\Builder\Attachment\ReferenceAttachmentBuilder;
use PrasadChinwal\MicrosoftGraph\Endpoints\Attachment;

beforeEach(function () {
    Cache::put('microsoft_graph_access_token', 'fake-token', 3600);
    Cache::put('microsoft_graph_token_expiry', now()->addHour()->timestamp, 3600);
});

test('forMessage()->list() returns collection of attachments', function () {
    Http::fake([
        'graph.microsoft.com/v1.0/users/*/messages/*/attachments' => Http::response([
            'value' => [
                ['id' => 'att-1', 'name' => 'doc.pdf', 'contentType' => 'application/pdf', 'size' => 1000],
                ['id' => 'att-2', 'name' => 'image.png', 'contentType' => 'image/png', 'size' => 2000],
            ],
        ], 200),
    ]);

    $attachments = (new Attachment)
        ->forMessage('john.doe@company.com', 'msg-1')
        ->list();

    expect($attachments)->toHaveCount(2)
        ->and($attachments->first()->name)->toBe('doc.pdf');
});

test('forEvent()->list() returns event attachments', function () {
    Http::fake([
        'graph.microsoft.com/v1.0/users/*/events/*/attachments' => Http::response([
            'value' => [
                ['id' => 'att-3', 'name' => 'notes.txt', 'contentType' => 'text/plain', 'size' => 500],
            ],
        ], 200),
    ]);

    $attachments = (new Attachment)
        ->forEvent('john.doe@company.com', 'evt-1')
        ->list();

    expect($attachments)->toHaveCount(1)
        ->and($attachments->first()->name)->toBe('notes.txt');
});

test('forMessage()->get() returns single attachment', function () {
    Http::fake([
        'graph.microsoft.com/v1.0/users/*/messages/*/attachments/*' => Http::response([
            'id' => 'att-1',
            'name' => 'doc.pdf',
            'contentType' => 'application/pdf',
            'size' => 1000,
        ], 200),
    ]);

    $attachment = (new Attachment)
        ->forMessage('john.doe@company.com', 'msg-1')
        ->get('att-1');

    expect($attachment->id)->toBe('att-1')
        ->and($attachment->name)->toBe('doc.pdf')
        ->and($attachment->contentType)->toBe('application/pdf');
});

test('forMessage()->getRaw() returns raw response with content', function () {
    Http::fake(function ($request) {
        if (str_contains($request->url(), '$value')) {
            return Http::response('raw-binary-data', 200, ['Content-Type' => 'application/pdf']);
        }
    });

    $response = (new Attachment)
        ->forMessage('john.doe@company.com', 'msg-1')
        ->getRaw('att-1');

    expect($response->status())->toBe(200)
        ->and($response->body())->toBe('raw-binary-data')
        ->and($response->header('Content-Type'))->toBe('application/pdf');
});

test('forMessage()->addFile() adds file attachment', function () {
    Http::fake([
        'graph.microsoft.com/v1.0/users/*/messages/*/attachments' => Http::response([
            'id' => 'att-new',
            'name' => 'document.pdf',
            'contentType' => 'application/pdf',
            'size' => 100,
        ], 201),
    ]);

    $builder = FileAttachmentBuilder::fromData('content', 'document.pdf', 'application/pdf');
    $attachment = (new Attachment)
        ->forMessage('john.doe@company.com', 'msg-1')
        ->addFile($builder);

    expect($attachment->id)->toBe('att-new')
        ->and($attachment->name)->toBe('document.pdf');
});

test('forMessage()->addReference() adds reference attachment', function () {
    Http::fake([
        'graph.microsoft.com/v1.0/users/*/messages/*/attachments' => Http::response([
            'id' => 'att-ref',
            'name' => 'Report',
            'contentType' => 'reference',
            'size' => 0,
        ], 201),
    ]);

    $builder = ReferenceAttachmentBuilder::fromUrl('https://contoso.sharepoint.com/report.xlsx', 'Report')
        ->setProvider('oneDriveBusiness')
        ->setPermission('edit');
    $attachment = (new Attachment)
        ->forMessage('john.doe@company.com', 'msg-1')
        ->addReference($builder);

    expect($attachment->id)->toBe('att-ref')
        ->and($attachment->name)->toBe('Report');
});

test('forMessage()->create() adds attachment from raw data', function () {
    Http::fake([
        'graph.microsoft.com/v1.0/users/*/messages/*/attachments' => Http::response([
            'id' => 'att-custom',
            'name' => 'custom.txt',
            'contentType' => 'text/plain',
            'size' => 13,
        ], 201),
    ]);

    $attachment = (new Attachment)
        ->forMessage('john.doe@company.com', 'msg-1')
        ->create([
            '@odata.type' => '#microsoft.graph.fileAttachment',
            'name' => 'custom.txt',
            'contentBytes' => base64_encode('Hello, World!'),
            'contentType' => 'text/plain',
        ]);

    expect($attachment->id)->toBe('att-custom')
        ->and($attachment->name)->toBe('custom.txt');
});

test('forMessage()->delete() returns true on success', function () {
    Http::fake([
        'graph.microsoft.com/v1.0/users/*/messages/*/attachments/*' => Http::response('', 204),
    ]);

    $result = (new Attachment)
        ->forMessage('john.doe@company.com', 'msg-1')
        ->delete('att-1');

    expect($result)->toBeTrue();
});

test('forEvent()->delete() deletes event attachment', function () {
    Http::fake([
        'graph.microsoft.com/v1.0/users/*/events/*/attachments/*' => Http::response('', 204),
    ]);

    $result = (new Attachment)
        ->forEvent('john.doe@company.com', 'evt-1')
        ->delete('att-1');

    expect($result)->toBeTrue();
});

test('forMessage()->delete() throws exception on non-204', function () {
    Http::fake([
        'graph.microsoft.com/v1.0/users/*/messages/*/attachments/*' => Http::response('', 404),
    ]);

    (new Attachment)
        ->forMessage('john.doe@company.com', 'msg-1')
        ->delete('att-1');
})->throws(\Illuminate\Http\Client\RequestException::class);

test('operations without context throw runtime exception', function () {
    $attachment = new Attachment();

    $attachment->list();
})->throws(\RuntimeException::class, 'Context not set');

test('forMessage() validates email', function () {
    (new Attachment)->forMessage('invalid-email', 'msg-id');
})->throws(\PrasadChinwal\MicrosoftGraph\Exceptions\InvalidEmailException::class);

test('forMessage() validates empty email', function () {
    (new Attachment)->forMessage('', 'msg-id');
})->throws(\PrasadChinwal\MicrosoftGraph\Exceptions\InvalidEmailException::class);
