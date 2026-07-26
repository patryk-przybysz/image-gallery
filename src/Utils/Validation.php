<?php

declare(strict_types=1);

namespace App\Utils;

use Chubbyphp\Parsing\Error;
use Chubbyphp\Parsing\ErrorsException;
use Chubbyphp\Parsing\Parser;
use Chubbyphp\Parsing\Schema\SchemaInterface;

/**
 * Thin helpers around chubbyphp-parsing for form-shaped error trees.
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
     * Required non-empty string. Missing/null inputs become '' via default, then fail with $message.
     */
    public static function requiredString(string $message): SchemaInterface
    {
        return self::refine(
            self::parser()->string()->default(''),
            static fn (string $value): bool => $value !== '',
            $message,
        );
    }
}
