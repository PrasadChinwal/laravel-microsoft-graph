<?php

use PrasadChinwal\MicrosoftGraph\Builder\Attachment\FileAttachmentBuilder;

beforeEach(function () {
    $this->tempFile = tempnam(sys_get_temp_dir(), 'graph_test_');
    file_put_contents($this->tempFile, 'test file content');
});

afterEach(function () {
    if (file_exists($this->tempFile)) {
        unlink($this->tempFile);
    }
});

test('fromFile creates builder with correct properties', function () {
    $builder = FileAttachmentBuilder::fromFile($this->tempFile, 'test.txt');

    expect($builder->name)->toBe('test.txt')
        ->and($builder->contentBytes)->toBe(base64_encode('test file content'))
        ->and($builder->contentType)->toBe('text/plain')
        ->and($builder->size)->toBe(strlen('test file content'))
        ->and($builder->isInline)->toBeFalse();
});

test('fromFile uses basename when name not provided', function () {
    $builder = FileAttachmentBuilder::fromFile($this->tempFile);

    expect($builder->name)->toBe(basename($this->tempFile));
});

test('fromFile throws exception for non-existent file', function () {
    FileAttachmentBuilder::fromFile('/tmp/non_existent_file_12345.txt');
})->throws(\InvalidArgumentException::class, 'File not found');

test('fromFile throws exception for file exceeding 3MB', function () {
    $largeFile = tempnam(sys_get_temp_dir(), 'large_');
    file_put_contents($largeFile, str_repeat('a', 4 * 1024 * 1024));

    FileAttachmentBuilder::fromFile($largeFile);
})->throws(\InvalidArgumentException::class, 'exceeds 3MB');

test('fromData creates builder with correct properties', function () {
    $data = 'hello world';
    $builder = FileAttachmentBuilder::fromData($data, 'hello.txt', 'text/plain');

    expect($builder->name)->toBe('hello.txt')
        ->and($builder->contentBytes)->toBe(base64_encode($data))
        ->and($builder->contentType)->toBe('text/plain')
        ->and($builder->size)->toBe(strlen($data));
});

test('fromData uses default content type when not provided', function () {
    $builder = FileAttachmentBuilder::fromData('data', 'file.bin');

    expect($builder->contentType)->toBe('application/octet-stream');
});

test('fromData throws exception for data exceeding 3MB', function () {
    FileAttachmentBuilder::fromData(str_repeat('a', 4 * 1024 * 1024), 'large.bin');
})->throws(\InvalidArgumentException::class, 'exceeds 3MB');

test('asInline marks attachment as inline with content ID', function () {
    $builder = FileAttachmentBuilder::fromData('data', 'img.png')
        ->asInline('logo-cid');

    expect($builder->isInline)->toBeTrue()
        ->and($builder->contentId)->toBe('logo-cid');
});

test('toArray returns correct structure for file attachment', function () {
    $data = 'file content';
    $builder = FileAttachmentBuilder::fromData($data, 'doc.txt', 'text/plain');

    $result = $builder->toArray();

    expect($result)->toMatchArray([
        '@odata.type' => '#microsoft.graph.fileAttachment',
        'name' => 'doc.txt',
        'contentBytes' => base64_encode($data),
        'contentType' => 'text/plain',
    ]);
    expect($result)->not->toHaveKey('isInline');
});

test('toArray includes inline fields when set', function () {
    $builder = FileAttachmentBuilder::fromData('data', 'img.png', 'image/png')
        ->asInline('logo-cid');

    $result = $builder->toArray();

    expect($result)->toMatchArray([
        '@odata.type' => '#microsoft.graph.fileAttachment',
        'isInline' => true,
        'contentId' => 'logo-cid',
    ]);
});
