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
class Some extends Option
{
    /** @var T */
    private mixed $inner;

    /**
     * @psalm-param T $inner
     */
    public function __construct(mixed $inner)
    {
        $this->inner = $inner;
    }

    /**
     * @psalm-return T
     */
    public function unwrap(): mixed
    {
        return $this->inner;
    }

    public function expect(string $error): mixed
    {
        return $this->inner;
    }

    /**
     * @psalm-template U
     * @psalm-param callable(T): U $callback
     * @psalm-return Option<U>
     */
    public function map(callable $callback): Option
    {
        //TODO: add lazy evaluation. It should be a non-breaking change.
        return new self($callback($this->inner));
    }

    public function isSome(): bool
    {
        return true;
    }

    public function unwrap_or(mixed $default): mixed
    {
        if ($default === null) {
            Panic::panic("Null passed as a default parameter");
        }

        return $this->inner;
    }

    public function unwrap_or_else(callable $default): mixed
    {
        return $this->inner;
    }

    public function filter(callable $callback): Option
    {
        if ($callback($this->inner)) {
            return $this;
        }

        return Option::None();
    }

    public function flatten(): Option
    {
        if ($this->inner instanceof Option) {
            return $this->inner;
        }

        return $this;
    }

    public function mapOr(mixed $default, callable $callback): mixed
    {
        return $callback($this->inner);
    }

    public function andThen(callable $callback): mixed
    {
        $result = $callback($this->inner);
        if (!($result instanceof Option)) {
            Panic::panic("Invalid callback return value");
        }

        return $result;
    }

    public function orElse(callable $callback): mixed
    {
        return $this;
    }

    /**
     * @return Iter
     */
    public function iter(): Iter
    {
        return Iterator::from([$this->inner]);
    }

    /**
     * PARTIAL-IMPLEMENTATION: Replaces the value in the option with a new one
     *
     * This can not be properly represented in PHP. Rust STD uses mem::replace(self, Some(new)) for this.
     * In other words, the option replaces itself with an entirely new instance. This is not possible in PHP, so we have no way of going from None -> Some
     * We can however do Some -> Some, so for now this is directly implemented on Option\Some only
     *
     * @param mixed $value
     * @return Option
     */
    // The only way I can currently see this working is to flatten Some and None into Option - but we would lose ability
    // to compare None === None... None == None would still work though, and so would Some(1) == Some(1)
    public function replace(mixed $value): Option
    {
        Panic::panicIfNull($value);

        $oldVal = $this->inner;
        $this->inner = $value;

        return Option::Some($oldVal);
    }
}
