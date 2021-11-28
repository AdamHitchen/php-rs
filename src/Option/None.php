<?php
declare(strict_types=1);

namespace PhpRs\Option;

use PhpRs\Iterator;
use PhpRs\Iterator\Iter;
use PhpRs\Option;
use PhpRs\PanicHandler\Panic;

/**
 * @template T
 * @psalm-template T
 */
class None extends Option
{
    /**
     * @internal
     * @return static
     */
    public static function instance(): self
    {
        static $none;
        if (!isset($none)) {
            $none = new None();
        }

        return $none;
    }

    public function map(callable $callback): Option
    {
        return $this;
    }

    public function isSome(): bool
    {
        return false;
    }

    /**
     * @psalm-template U
     * @template U
     * @psalm-param callable(T): U $callback
     * @return Option<U>
     * @psalm-return Option<U>
     */
    public function unwrap(): mixed
    {
        // function will never actually return, but this makes php-code-coverage happy ;)
        return Panic::panic("Unwrapped a none value");
    }

    public function expect(string $error): mixed
    {
        return Panic::panic($error);
    }

    public function unwrap_or(mixed $default): mixed
    {
        if ($default === null) {
            Panic::panic("Null passed as a default parameter");
        }

        return $default;
    }

    public function unwrap_or_else(callable $default): mixed
    {
        $value = $default();
        if ($value === null) {
            Panic::panic("Null passed as a default parameter");
        }

        return $value;
    }

    public function filter(callable $callback): Option
    {
        return Option::None();
    }

    public function flatten(): Option
    {
        return Option::None();
    }

    public function mapOr(mixed $default, callable $callback): mixed
    {
        return $default;
    }

    public function andThen(callable $callback): mixed
    {
        return Option::None();
    }

    public function orElse(callable $callback): mixed
    {
        $result = $callback();
        if (!($result instanceof Option)) {
            Panic::panic("Result of callback not Option");
        }

        return $result;
    }

    public function iter(): Iter
    {
        return Iterator::from([]);
    }
}