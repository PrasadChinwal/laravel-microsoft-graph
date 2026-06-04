<?php

use PrasadChinwal\MicrosoftGraph\Builder\User\User;

test('user builder has all required properties with null defaults', function () {
    $user = new User();

    expect($user->displayName)->toBeNull()
        ->and($user->jobTitle)->toBeNull()
        ->and($user->department)->toBeNull()
        ->and($user->mobilePhone)->toBeNull()
        ->and($user->givenName)->toBeNull()
        ->and($user->surname)->toBeNull()
        ->and($user->accountEnabled)->toBeNull()
        ->and($user->mail)->toBeNull()
        ->and($user->city)->toBeNull()
        ->and($user->companyName)->toBeNull()
        ->and($user->country)->toBeNull()
        ->and($user->employeeId)->toBeNull()
        ->and($user->officeLocation)->toBeNull()
        ->and($user->postalCode)->toBeNull()
        ->and($user->state)->toBeNull()
        ->and($user->streetAddress)->toBeNull()
        ->and($user->usageLocation)->toBeNull()
        ->and($user->preferredLanguage)->toBeNull()
        ->and($user->aboutMe)->toBeNull()
        ->and($user->employeeHireDate)->toBeNull()
        ->and($user->employeeLeaveDateTime)->toBeNull()
        ->and($user->employeeType)->toBeNull()
        ->and($user->ageGroup)->toBeNull()
        ->and($user->birthday)->toBeNull()
        ->and($user->preferredName)->toBeNull()
        ->and($user->mailNickname)->toBeNull()
        ->and($user->mySite)->toBeNull();
});

test('user builder array properties default to empty', function () {
    $user = new User();

    expect($user->businessPhones)->toBe([])
        ->and($user->otherMails)->toBe([])
        ->and($user->pastProjects)->toBe([])
        ->and($user->responsibilities)->toBe([])
        ->and($user->schools)->toBe([])
        ->and($user->interests)->toBeNull();
});

test('user builder properties can be set directly', function () {
    $user = new User();
    $user->displayName = 'Jane Smith';
    $user->jobTitle = 'Engineer';
    $user->department = 'Engineering';
    $user->mobilePhone = '+1 234 567 8901';

    expect($user->displayName)->toBe('Jane Smith')
        ->and($user->jobTitle)->toBe('Engineer')
        ->and($user->department)->toBe('Engineering')
        ->and($user->mobilePhone)->toBe('+1 234 567 8901');
});
