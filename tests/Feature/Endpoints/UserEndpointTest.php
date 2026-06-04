<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PrasadChinwal\MicrosoftGraph\Builder\License\AssignLicenseBuilder;
use PrasadChinwal\MicrosoftGraph\Builder\License\NewLicense;
use PrasadChinwal\MicrosoftGraph\Builder\License\NewLicenseCollection;
use PrasadChinwal\MicrosoftGraph\Endpoints\User;

beforeEach(function () {
    Cache::put('microsoft_graph_access_token', 'fake-token', 3600);
    Cache::put('microsoft_graph_token_expiry', now()->addHour()->timestamp, 3600);
});

test('users()->get() returns collection of users', function () {
    Http::fake([
        'graph.microsoft.com/v1.0/users' => Http::response([
            'value' => [
                ['id' => '1', 'displayName' => 'User One', 'mail' => 'user1@test.com', 'userPrincipalName' => 'user1@test.com', 'businessPhones' => []],
                ['id' => '2', 'displayName' => 'User Two', 'mail' => 'user2@test.com', 'userPrincipalName' => 'user2@test.com', 'businessPhones' => []],
            ],
        ], 200),
    ]);

    $users = (new User)->get();

    expect($users)->toHaveCount(2);
});

test('users()->find() returns a single user', function () {
    Http::fake([
        'graph.microsoft.com/v1.0/users/john.doe@company.com' => Http::response([
            'id' => '3',
            'displayName' => 'John Doe',
            'mail' => 'john.doe@company.com',
            'jobTitle' => 'Engineer',
            'department' => 'Engineering',
            'mobilePhone' => '+1 234 567 8900',
            'userPrincipalName' => 'john.doe@company.com',
            'businessPhones' => ['+1 234 567 8900'],
        ], 200),
    ]);

    $user = (new User)->find('john.doe@company.com');

    expect($user->displayName)->toBe('John Doe')
        ->and($user->jobTitle)->toBe('Engineer')
        ->and($user->department)->toBe('Engineering');
});

test('users()->find() throws InvalidEmailException for empty email', function () {
    (new User)->find('');
})->throws(\PrasadChinwal\MicrosoftGraph\Exceptions\InvalidEmailException::class, 'cannot be empty');

test('users()->find() throws InvalidEmailException for invalid email format', function () {
    (new User)->find('not-an-email');
})->throws(\PrasadChinwal\MicrosoftGraph\Exceptions\InvalidEmailException::class, 'not valid');

test('users()->update() returns true on success', function () {
    Http::fake([
        'graph.microsoft.com/v1.0/users/jane.smith@company.com' => Http::response('', 204),
    ]);

    $userUpdate = new \PrasadChinwal\MicrosoftGraph\Builder\User\User();
    $userUpdate->displayName = 'Jane Smith';
    $userUpdate->jobTitle = 'Senior Engineer';

    $result = (new User)->update('jane.smith@company.com', $userUpdate);

    expect($result)->toBeTrue();
});

test('users()->update() sends PATCH with filtered data', function () {
    Http::fake(function ($request) {
        if ($request->method() === 'PATCH') {
            $body = json_decode($request->body(), true);
            expect($body)->toMatchArray([
                'displayName' => 'Jane Smith',
                'jobTitle' => 'Senior Engineer',
            ])
                ->and($body)->not->toHaveKey('aboutMe');

            return Http::response('', 204);
        }
    });

    $userUpdate = new \PrasadChinwal\MicrosoftGraph\Builder\User\User();
    $userUpdate->displayName = 'Jane Smith';
    $userUpdate->jobTitle = 'Senior Engineer';

    (new User)->update('jane.smith@company.com', $userUpdate);
});

test('withEmail()->getPhoto() returns photo response', function () {
    Http::fake([
        'graph.microsoft.com/v1.0/users/john.doe@company.com/photo/*' => Http::response('binary-image-data', 200, ['Content-Type' => 'image/jpeg']),
    ]);

    $response = (new User)->withEmail('john.doe@company.com')->getPhoto();

    expect($response->status())->toBe(200)
        ->and($response->body())->toBe('binary-image-data');
});

test('withEmail()->updatePhoto() uploads photo', function () {
    Http::fake([
        'graph.microsoft.com/v1.0/users/john.doe@company.com/photo/*' => Http::response('', 200),
    ]);

    $response = (new User)->withEmail('john.doe@company.com')->updatePhoto('fake-image-data');

    expect($response->status())->toBe(200);
});

test('withEmail()->deletePhoto() returns true', function () {
    Http::fake([
        'graph.microsoft.com/v1.0/users/john.doe@company.com/photo/*' => Http::response('', 204),
    ]);

    $result = (new User)->withEmail('john.doe@company.com')->deletePhoto();

    expect($result)->toBeTrue();
});

test('withEmail()->getLicenses() returns license details', function () {
    Http::fake([
        'graph.microsoft.com/v1.0/users/john.doe@company.com/licenseDetails' => Http::response([
            'value' => [
                [
                    'id' => 'license-1',
                    'skuId' => 'sku-123',
                    'skuPartNumber' => 'ENTERPRISEPACK',
                    'servicePlans' => [],
                ],
            ],
        ], 200),
    ]);

    $licenses = (new User)->withEmail('john.doe@company.com')->getLicenses();

    expect($licenses)->toHaveCount(1);
});

test('withEmail()->assignLicense() assigns license and returns user', function () {
    Http::fake([
        'graph.microsoft.com/v1.0/users/john.doe@company.com/assignLicense' => Http::response([
            'id' => 'user-1',
            'displayName' => 'John Doe',
            'userPrincipalName' => 'john.doe@company.com',
            'businessPhones' => [],
        ], 200),
    ]);

    $license = new NewLicense([], 'sku-123');
    $collection = new NewLicenseCollection([$license]);
    $builder = new AssignLicenseBuilder($collection, []);

    $user = (new User)->withEmail('john.doe@company.com')->assignLicense($builder);

    expect($user->displayName)->toBe('John Doe');
});

test('withEmail() returns self for chaining', function () {
    $user = new User();
    expect($user->withEmail('test@test.com'))->toBe($user);
});
