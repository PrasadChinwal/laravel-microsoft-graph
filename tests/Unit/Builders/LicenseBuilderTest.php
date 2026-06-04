<?php

use PrasadChinwal\MicrosoftGraph\Builder\License\AssignLicenseBuilder;
use PrasadChinwal\MicrosoftGraph\Builder\License\NewLicense;
use PrasadChinwal\MicrosoftGraph\Builder\License\NewLicenseCollection;

test('new license can be created with disabled plans and sku id', function () {
    $license = new NewLicense(['plan-1', 'plan-2'], 'sku-123');

    expect($license->disabledPlans)->toBe(['plan-1', 'plan-2'])
        ->and($license->skuId)->toBe('sku-123');
});

test('new license static factory method', function () {
    $license = NewLicense::make(['plan-1'], 'sku-456');

    expect($license)->toBeInstanceOf(NewLicense::class)
        ->and($license->skuId)->toBe('sku-456');
});

test('new license collection extends collection', function () {
    $license = new NewLicense([], 'sku-1');
    $collection = new NewLicenseCollection([$license]);

    expect($collection)->toBeInstanceOf(\Illuminate\Support\Collection::class)
        ->and($collection)->toHaveCount(1);
});

test('assign license builder stores add licenses and remove licenses', function () {
    $license1 = new NewLicense([], 'sku-1');
    $license2 = new NewLicense(['plan-x'], 'sku-2');
    $collection = new NewLicenseCollection([$license1, $license2]);

    $builder = new AssignLicenseBuilder($collection, ['sku-3']);

    expect($builder->addLicenses)->toHaveCount(2)
        ->and($builder->removeLicenses)->toBe(['sku-3']);
});

test('assign license builder static make method', function () {
    $license = new NewLicense([], 'sku-1');
    $collection = new NewLicenseCollection([$license]);

    $builder = AssignLicenseBuilder::make($collection, ['sku-2']);

    expect($builder)->toBeInstanceOf(AssignLicenseBuilder::class)
        ->and($builder->addLicenses)->toHaveCount(1)
        ->and($builder->removeLicenses)->toBe(['sku-2']);
});

test('assign license builder defaults to empty remove licenses', function () {
    $license = new NewLicense([], 'sku-1');
    $collection = new NewLicenseCollection([$license]);

    $builder = new AssignLicenseBuilder($collection);

    expect($builder->removeLicenses)->toBe([]);
});
