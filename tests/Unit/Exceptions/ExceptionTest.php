<?php

use PrasadChinwal\MicrosoftGraph\Exceptions\AuthenticationException;
use PrasadChinwal\MicrosoftGraph\Exceptions\ConfigurationException;
use PrasadChinwal\MicrosoftGraph\Exceptions\InvalidEmailException;
use PrasadChinwal\MicrosoftGraph\Exceptions\MicrosoftGraphException;

test('MicrosoftGraphException is the base exception', function () {
    $exception = new MicrosoftGraphException('Graph error');

    expect($exception)->toBeInstanceOf(\Exception::class)
        ->and($exception->getMessage())->toBe('Graph error');
});

test('ConfigurationException missingCredentials creates correct message', function () {
    $exception = ConfigurationException::missingCredentials('TENANT_ID');

    expect($exception)->toBeInstanceOf(MicrosoftGraphException::class)
        ->and($exception->getMessage())->toContain('TENANT_ID')
        ->and($exception->getMessage())->toContain('not set')
        ->and($exception->getMessage())->toContain('.env');
});

test('ConfigurationException invalid creates correct message', function () {
    $exception = ConfigurationException::invalid('timezone', 'Invalid timezone identifier');

    expect($exception)->toBeInstanceOf(MicrosoftGraphException::class)
        ->and($exception->getMessage())->toContain('timezone')
        ->and($exception->getMessage())->toContain('Invalid timezone identifier');
});

test('InvalidEmailException empty creates correct message', function () {
    $exception = InvalidEmailException::empty();

    expect($exception)->toBeInstanceOf(MicrosoftGraphException::class)
        ->and($exception->getMessage())->toBe('Email address cannot be empty.');
});

test('InvalidEmailException invalidFormat creates correct message', function () {
    $exception = InvalidEmailException::invalidFormat('not-an-email');

    expect($exception)->toBeInstanceOf(MicrosoftGraphException::class)
        ->and($exception->getMessage())->toContain('not-an-email')
        ->and($exception->getMessage())->toContain('not valid');
});

test('AuthenticationException tokenFetchFailed creates correct message', function () {
    $exception = AuthenticationException::tokenFetchFailed('HTTP 401');

    expect($exception)->toBeInstanceOf(MicrosoftGraphException::class)
        ->and($exception->getMessage())->toContain('Failed to fetch access token')
        ->and($exception->getMessage())->toContain('HTTP 401');
});

test('AuthenticationException tokenFetchFailed without reason', function () {
    $exception = AuthenticationException::tokenFetchFailed();

    expect($exception->getMessage())->toBe('Failed to fetch access token from Microsoft Graph API.');
});

test('AuthenticationException tokenExpired creates correct message', function () {
    $exception = AuthenticationException::tokenExpired();

    expect($exception)->toBeInstanceOf(MicrosoftGraphException::class)
        ->and($exception->getMessage())->toContain('expired');
});
