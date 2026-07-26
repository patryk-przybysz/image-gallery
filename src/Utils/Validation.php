<?php

declare(strict_types=1);

namespace App\Utils;

use Chubbyphp\Parsing\Error;
use Chubbyphp\Parsing\ErrorsException;
use Chubbyphp\Parsing\Parser;
use Chubbyphp\Parsing\Schema\SchemaInterface;

/**
 * Thin helpers around chubbyphp-parsing for form-shaped error trees.
 *
 * Prefer Parser built-ins (string, int, assoc, minLength, …) for structure.
 * Use refine() only for app-specific predicates. Use minLength()/oneOf() when
 * a built-in constraint needs a user-facing message (chubbyphp has no message API).
 */
final class Validation
{
    private static ?Parser $parser = null;

    public static function parser(): Parser
    {
        return self::$parser ??= new Parser();
    }

    /**
     * @return array<int|string, mixed> Nested message tree compatible with format_errors / empty_recursive; [] when valid
     */
    public static function errors(SchemaInterface $schema, mixed $data): array
    {
        $result = $schema->safeParse($data);

        if ($result->success) {
            return [];
        }

        $tree = $result->exception->errors->toTree();

        // Root-level (non-object) errors use an empty path key in toTree().
        if (array_keys($tree) === [''] && is_array($tree[''])) {
            return $tree[''];
        }

        return $tree;
    }

    /**
     * App-specific predicate (DB uniqueness, cross-field checks, upload rules, …).
     */
    public static function refine(SchemaInterface $schema, callable $predicate, string $message): SchemaInterface
    {
        return $schema->postParse(static function (mixed $value) use ($predicate, $message) {
            if (!$predicate($value)) {
                throw new ErrorsException(new Error('custom.refine', $message, []));
            }

            return $value;
        });
    }

    /**
     * Required non-empty string. Missing/null → '' via default(), then fail with $message.
     */
    public static function requiredString(string $message): SchemaInterface
    {
        return self::minLength(self::parser()->string()->default(''), 1, $message);
    }

    /**
     * Same check as StringSchema::minLength(), with a form-friendly message.
     */
    public static function minLength(SchemaInterface $schema, int $minLength, string $message): SchemaInterface
    {
        return $schema->postParse(static function (string $value) use ($minLength, $message) {
            if (strlen($value) >= $minLength) {
                return $value;
            }

            throw new ErrorsException(new Error('string.minLength', $message, [
                'minLength' => $minLength,
                'given' => strlen($value),
            ]));
        });
    }

    /**
     * Strict enum membership with a single form-friendly message.
     *
     * @param list<mixed> $allowed
     */
    public static function oneOf(SchemaInterface $schema, array $allowed, string $message): SchemaInterface
    {
        return $schema->postParse(static function (mixed $value) use ($allowed, $message) {
            if (in_array($value, $allowed, true)) {
                return $value;
            }

            throw new ErrorsException(new Error('custom.oneOf', $message, [
                'allowed' => $allowed,
                'given' => $value,
            ]));
        });
    }
}
