<?php

use PrasadChinwal\MicrosoftGraph\Traits\HasQueryFilters;

beforeEach(function () {
    $this->trait = new class {
        use HasQueryFilters;

        public function getFilter(): ?string
        {
            return $this->filter;
        }
    };
});

test('where adds single filter condition', function () {
    $this->trait->where('subject', 'eq', 'Meeting');

    expect($this->trait->getFilter())->toBe("subject eq 'Meeting'");
});

test('where chains multiple conditions with and', function () {
    $this->trait
        ->where('start/dateTime', 'ge', '2024-01-01')
        ->where('end/dateTime', 'le', '2024-12-31');

    expect($this->trait->getFilter())
        ->toBe("start/dateTime ge '2024-01-01' and end/dateTime le '2024-12-31'");
});

test('orWhere adds condition with or', function () {
    $this->trait
        ->where('subject', 'eq', 'Meeting')
        ->orWhere('subject', 'eq', 'Workshop');

    expect($this->trait->getFilter())
        ->toBe("subject eq 'Meeting' or subject eq 'Workshop'");
});

test('where with no prior filter starts fresh', function () {
    $this->trait->where('id', 'eq', '123');

    expect($this->trait->getFilter())->toBe("id eq '123'");
});

test('orWhere alone (without prior where) works', function () {
    $this->trait->orWhere('name', 'eq', 'Test');

    expect($this->trait->getFilter())->toBe("name eq 'Test'");
});

test('filter returns null initially', function () {
    expect($this->trait->getFilter())->toBeNull();
});

test('complex filter with multiple conditions', function () {
    $this->trait
        ->where('department', 'eq', 'Engineering')
        ->orWhere('department', 'eq', 'Marketing')
        ->where('country', 'eq', 'US');

    expect($this->trait->getFilter())
        ->toBe("department eq 'Engineering' or department eq 'Marketing' and country eq 'US'");
});
