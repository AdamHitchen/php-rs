<?php
declare(strict_types=1);

namespace PhpRs;

use Iterator;
use PhpRs\Iterator\Iter;
use PhpRs\Option\None;
use PhpRs\Option\Some;
use PhpRs\PanicHandler\Panic;

/**
 * Change to enum if https://wiki.php.net/rfc/tagged_unions lands.
 * @template T
 * @psalm-template T
 */
abstract class Option
{
    /**
     * @psalm-template U
     * @psalm-param callable(T): U $callback
     * @psalm-return Option<U>
     */
    public abstract function map(callable $callback): Option;

    /**
     * @psalm-template U
     * @psalm-param U $default
     * @psalm-param callable(T): U $callback
     * @psalm-return U
     */
    public abstract function mapOr(mixed $default, callable $callback): mixed;

    /**
     * @psalm-template U
     * @psalm-param callable(T): Option<U> $callback
     * @psalm-return Option<U>
     */
    public abstract function andThen(callable $callback): mixed;

    /**
     * @psalm-param callable(): Option<T> $callback
     * @psalm-return Option<T>
     */
    public abstract function orElse(callable $callback): mixed;

    /**
     * @psalm-return Option<T>
     */
    public static function None(): Option
    {
        return None::instance();
    }

    public abstract function isSome(): bool;

    /**
     * @return T
     * @psalm-return T
     */
    public abstract function unwrap(): mixed;

    /**
     * @param string $error
     * @return T
     * @psalm-return T
     */
    public abstract function expect(string $error): mixed;

    /**
     * @param T $default
     * @return T
     * @psalm-return T
     */
    public abstract function unwrap_or(mixed $default): mixed;

    public abstract function iter(): Iter;


    /**
     * @param callable(): T $default
     * @return T
     */
    public abstract function unwrap_or_else(callable $default): mixed;

    /**
     * @param callable(T): bool $callback
     * @return Option<T>
     */
    public abstract function filter(callable $callback): Option;

    /**
     * @template U
     * @return Option<U>
     */
    public abstract function flatten(): Option;

    public function isNone(): bool {
        return !$this->isSome();
    }

    // Not currently implementable
    //public abstract function replace(mixed $value): Option;

    /**
     * @psalm-template V
     * @psalm-param V $value
     * @psalm-return Option<V>
     */
    public static function Some($value): Option
    {
        if ($value === null) {
            Panic::panic("Attempted to create Some with null");
        }

        return new Some($value);
    }

    /**
     * Convenience method which can turn null to None
     * @psalm-template V
     * @psalm-param V $x
     * @psalm-return Option<V>
     */
    public static function from(mixed $x): self
    {
        if ($x === null) {
            return self::None();
        }

        return self::Some($x);
    }
}