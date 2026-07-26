<?php

declare(strict_types=1);

use App\Utils\ValidationSchema;

it('rejects missing required values', function () {
    $schema = (new ValidationSchema())->required();

    expect($schema->safeParse(null))->toBe(['The field has to be set'])
        ->and($schema->safeParse(''))->toBe(['The field has to be set']);
});

it('accepts a present required value', function () {
    $schema = (new ValidationSchema())->required();

    expect($schema->safeParse('ok'))->toBe([]);
});

it('accepts only values in the enum', function () {
    $schema = (new ValidationSchema())->enum(['public', 'private']);

    expect($schema->safeParse('public'))->toBe([])
        ->and($schema->safeParse('private'))->toBe([])
        ->and($schema->safeParse('hidden'))->toBe(['The field is not a correct value'])
        ->and($schema->safeParse(0))->toBe(['The field is not a correct value']);
});

it('compares enum values strictly', function () {
    $schema = (new ValidationSchema())->enum([1, 2]);

    expect($schema->safeParse(1))->toBe([])
        ->and($schema->safeParse('1'))->toBe(['The field is not a correct value']);
});

it('enforces minimum string length', function () {
    $schema = (new ValidationSchema())->minLength(8);

    expect($schema->safeParse('short'))->toBe(['The field is too short'])
        ->and($schema->safeParse('longenough'))->toBe([]);
});

it('enforces maximum string length', function () {
    $schema = (new ValidationSchema())->maxLength(5);

    expect($schema->safeParse('toolong'))->toBe(['The field is too long'])
        ->and($schema->safeParse('ok'))->toBe([]);
});

it('validates nested array schemas per field', function () {
    $loginSchema = (new ValidationSchema())
        ->string()
        ->required('Please enter the login');

    $visibilitySchema = (new ValidationSchema())
        ->enum(['public', 'private']);

    $schema = (new ValidationSchema())->array([
        'login' => $loginSchema,
        'visibility' => $visibilitySchema,
    ]);

    expect($schema->safeParse([
        'login' => 'alice',
        'visibility' => 'public',
    ]))->toBe([
        'login' => [],
        'visibility' => [],
    ]);

    expect($schema->safeParse([
        'login' => '',
        'visibility' => 'hidden',
    ]))->toBe([
        'login' => ['Please enter the login'],
        'visibility' => ['The visibility is not a correct value'],
    ]);
});
