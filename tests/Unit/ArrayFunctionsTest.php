<?php

declare(strict_types=1);

use function App\Utils\empty_recursive;
use function App\Utils\is_associative_array;

it('treats empty scalars and empty nested arrays as empty', function () {
    expect(empty_recursive(null))->toBeTrue()
        ->and(empty_recursive(''))->toBeTrue()
        ->and(empty_recursive([]))->toBeTrue()
        ->and(empty_recursive([[], ['']]))->toBeTrue();
});

it('treats any non-empty nested value as not empty', function () {
    expect(empty_recursive(['ok']))->toBeFalse()
        ->and(empty_recursive([[], ['error']]))->toBeFalse()
        ->and(empty_recursive(['login' => [], 'password' => ['required']]))->toBeFalse();
});

it('detects associative arrays', function () {
    expect(is_associative_array(['login' => 'alice']))->toBeTrue()
        ->and(is_associative_array([1 => 'a', 0 => 'b']))->toBeTrue();
});

it('rejects list arrays and non-arrays as associative', function () {
    expect(is_associative_array(['a', 'b']))->toBeFalse()
        ->and(is_associative_array([]))->toBeFalse()
        ->and(is_associative_array('string'))->toBeFalse()
        ->and(is_associative_array(null))->toBeFalse();
});
