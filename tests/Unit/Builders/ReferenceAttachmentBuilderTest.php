<?php

use PrasadChinwal\MicrosoftGraph\Builder\Attachment\ReferenceAttachmentBuilder;

test('fromUrl creates builder with correct properties', function () {
    $builder = ReferenceAttachmentBuilder::fromUrl(
        'https://contoso.sharepoint.com/doc.pdf',
        'Report'
    );

    expect($builder->sourceUrl)->toBe('https://contoso.sharepoint.com/doc.pdf')
        ->and($builder->name)->toBe('Report');
});

test('setProvider sets provider type', function () {
    $builder = ReferenceAttachmentBuilder::fromUrl('https://example.com/f', 'f')
        ->setProvider('oneDriveBusiness');

    expect($builder->providerType)->toBe('oneDriveBusiness');
});

test('setPermission sets permission level', function () {
    $builder = ReferenceAttachmentBuilder::fromUrl('https://example.com/f', 'f')
        ->setPermission('edit');

    expect($builder->permission)->toBe('edit');
});

test('setThumbnail sets thumbnail URL', function () {
    $builder = ReferenceAttachmentBuilder::fromUrl('https://example.com/f', 'f')
        ->setThumbnail('https://example.com/thumb.png');

    expect($builder->thumbnailUrl)->toBe('https://example.com/thumb.png');
});

test('setPreview sets preview URL', function () {
    $builder = ReferenceAttachmentBuilder::fromUrl('https://example.com/f', 'f')
        ->setPreview('https://example.com/preview');

    expect($builder->previewUrl)->toBe('https://example.com/preview');
});

test('asFolder marks as folder reference', function () {
    $builder = ReferenceAttachmentBuilder::fromUrl('https://example.com/f', 'f')
        ->asFolder();

    expect($builder->isFolder)->toBeTrue();
});

test('fluent setters return self for chaining', function () {
    $builder = ReferenceAttachmentBuilder::fromUrl('https://example.com/f', 'f');

    expect($builder->setProvider('oneDriveBusiness'))->toBe($builder)
        ->and($builder->setPermission('edit'))->toBe($builder)
        ->and($builder->setThumbnail('https://thumb'))->toBe($builder)
        ->and($builder->setPreview('https://preview'))->toBe($builder)
        ->and($builder->asFolder())->toBe($builder);
});

test('toArray returns correct structure for reference attachment', function () {
    $builder = ReferenceAttachmentBuilder::fromUrl(
        'https://contoso.sharepoint.com/doc.pdf',
        'Report'
    );

    $result = $builder->toArray();

    expect($result)->toMatchArray([
        '@odata.type' => '#microsoft.graph.referenceAttachment',
        'name' => 'Report',
        'sourceUrl' => 'https://contoso.sharepoint.com/doc.pdf',
    ]);
});

test('toArray includes all optional fields when set', function () {
    $builder = ReferenceAttachmentBuilder::fromUrl('https://example.com/f', 'File')
        ->setProvider('oneDriveBusiness')
        ->setPermission('edit')
        ->setThumbnail('https://example.com/thumb.png')
        ->setPreview('https://example.com/preview')
        ->asFolder();

    $result = $builder->toArray();

    expect($result)->toMatchArray([
        '@odata.type' => '#microsoft.graph.referenceAttachment',
        'name' => 'File',
        'sourceUrl' => 'https://example.com/f',
        'providerType' => 'oneDriveBusiness',
        'permission' => 'edit',
        'thumbnailUrl' => 'https://example.com/thumb.png',
        'previewUrl' => 'https://example.com/preview',
        'isFolder' => true,
    ]);
});
