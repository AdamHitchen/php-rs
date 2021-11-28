<?php
declare(strict_types=1);

namespace PhpRs;

use ArrayIterator;
use PhpRs\Iterator\Chain;
use PhpRs\Iterator\Cloned;
use PhpRs\Iterator\Cycle;
use PhpRs\Iterator\Filter;
use PhpRs\Iterator\Iter;
use PhpRs\Iterator\Map;
use PhpRs\Iterator\OrderBy;
use PhpRs\Iterator\Repeat;
use PhpRs\Iterator\Skip;
use PhpRs\Iterator\SkipWhile;
use PhpRs\Option\Some;

/**
 * @template T
 */
class Iterator implements Iter
{
    /**
     * @param \Iterator<T> $iter
     */
    protected function __construct(
        protected \Iterator $iter
    ) {}

    /**
     * @template T
     * @param iterable<T> $iter
     * @return self<T>
     */
    public static function from(iterable $iter): self
    {
        return new self(self::buildIterFromIterable($iter));
    }

    public function cycle(): Cycle
    {
        return new Cycle($this);
    }

    /**
     * @template T
     * @param iterable<T> $iter
     * @return \Iterator<T>
     */
    private static function buildIterFromIterable(iterable $iter): \Iterator
    {
        if ($iter instanceof \Iterator) {
            return $iter;
        }

        /** @var ArrayIterator<T> $iter */
        return new ArrayIterator($iter);
    }

    /**
     * @return Option<T>
     */
    public function getNext(): Option
    {
        $value = $this->valid() ? Option::Some($this->current()) : Option::None();
        $this->next();
        $this->valid();
        return $value;
    }

    /**
     * @param \Iterator<T> $iterator
     * @return bool
     */
    public function eq(\Iterator $iterator): bool
    {
        $iter = Iterator::from($iterator);

        while ($this->getNext() == $iter->getNext()) {

            if (!$this->valid() && !$iter->valid()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Iterator<T> $iterator
     * @param callable(T, T): bool $callback
     * @return bool
     */
    public function eqBy(\Iterator $iterator, callable $callback): bool
    {
        $iter = Iterator::from($iterator);

        while ($this->valid() && $iter->valid()) {
            $val1 = $this->getNext();
            $val2 = $iter->getNext();

            if ($val1->isNone() || $val2->isNone()) {
                return false;
            }

            if (!$callback($val1->unwrap(), $val2->unwrap())) {
                return false;
            }
        }

        return $this->valid() === $iterator->valid();
    }

    /**
     * @template U
     * @param callable(T): U $c
     * @return Map<U>
     */
    public function map(callable $c): Map
    {
        return new Map($this, $c);
    }

    /**
     * @param callable(T): bool $c
     * @return Filter<T>
     */
    public function filter(callable $c): Filter
    {
        return new Filter($this, $c);
    }

    /**
     * @param callable(T): bool $c
     * @return Option<T>
     */
    public function find(callable $c): Option
    {
        while ($this->valid() && !$c($this->current())) {
            $this->next();
        }
        $value = $this->valid() ? Option::Some($this->current()) : Option::None();
        $this->next();

        return $value;
    }

    /**
     * @return T[]
     */
    public function collect(): array
    {
        $values = [];
        while ($this->valid()) {
            $values[] = $this->current();
            $this->next();
        }

        return $values;
    }

    /**
     * @return Option<T>
     */
    public function min(): Option
    {
        return $this->reduce(fn ($x, $y): mixed => min($x, $y));
    }

    public function max(): Option
    {
        return $this->reduce(fn ($x, $y): mixed => max($x, $y));
    }

    /**
     * @template U
     * @param callable(T, T): int $callback
     * @param OrderBy $orderBy
     * @return Option<T>
     */
    private function getByImpl(callable $callback, OrderBy $orderBy): Option
    {
        $match = Option::None();

        while ($this->valid()) {
            $value = $this->current();
            $this->next();

            if ($match->isNone()) {
                $match = Option::Some($value);
                continue;
            }

            $res = $callback($match->unwrap(), $value);
            $match = match ($orderBy) {
                OrderBy::Gt => $res === -1 ? Option::Some($value) : $match,
                OrderBy::Lt => $res === -1 ? $match : Option::Some($value),
            };
        }

        return $match;
    }

    /**
     * @template U
     * @param callable(T, T): int $callback
     * @return Option<T>
     */
    public function minBy(callable $callback): Option
    {
        return $this->getByImpl($callback, OrderBy::Lt);
    }

    /**
     * @template U
     * @param callable(T, T): int $callback
     * @return Option<T>
     */
    public function maxBy(callable $callback): Option
    {
        return $this->getByImpl($callback, OrderBy::Gt);
    }

    /**
     * @template U
     * @param callable(T): U $callback
     * @return Option<T>
     */
    private function getByKeyImpl(callable $callback, OrderBy $orderBy): Option
    {
        /**
         * @template U
         * @param T $val
         * @return array<U|T>
         */
        $keyAndVal = function (mixed $val) use ($callback): array {
            return [$callback($val), $val];
        };

        $spaceship = fn ($x, $y) => $x[0] <=> $y[0];
        $map = $this->map($keyAndVal);
        $result = match ($orderBy) {
            OrderBy::Gt => $map->maxBy($spaceship),
            OrderBy::Lt => $map->minBy($spaceship),
        };

        if ($result->isNone()) {
            return $result;
        }

        [, $res] = $result->unwrap();;


        return Option::Some($res);
    }

    /**
     * @template U
     * @param callable(T): U $callback
     * @return Option<T>
     */
    public function minByKey(callable $callback): Option
    {
        return $this->getByKeyImpl($callback, OrderBy::Lt);
    }

    /**
     * @template U
     * @param callable(T): U $callback
     * @return Option<T>
     */
    public function maxByKey(callable $callback): Option
    {
        return $this->getByKeyImpl($callback, OrderBy::Gt);
    }

    /**
     * Folds every element by applying an option and returns the final result
     *
     *
     * @template U
     * @param U $initial
     * @param callable(U, T): U $callback
     * @return U
     */
    public function fold(mixed $initial, callable $callback): mixed
    {
        while ($this->valid()) {
            $initial = $callback($initial, $this->current());
            $this->next();
        }

        return $initial;
    }

    /**
     * @param callable(T, T): T $callback
     * @return Option<T>
     */
    public function reduce(callable $callback): Option
    {
        $value = Option::None();

        while ($this->valid()) {
            $current = $this->current();
            $value = Option::Some($callback($value->unwrap_or($current), $current));
            $this->next();
        }

        return $value;
    }

    /**
     * @return array<mixed, T>
     */
    public function collectAssoc(): array
    {
        $values = [];
        while ($this->valid()) {
            $values[$this->key()] = $this->current();
            $this->next();
        }

        return $values;
    }

    /**
     * Clones the iterator value
     *
     * Clones the value yielded by the iterator. Note that this uses *clone* so a shallow clone will be created
     * unless __clone is implemented for the entire object tree.
     *
     * @return Cloned<T>
     */
    public function cloned(): Cloned
    {
        return new Cloned($this);
    }

    /**
     * @return Repeat<T>
     */
    public function repeat(): Repeat
    {
        return new Repeat($this);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->fold(0, fn (int $x): int => $x + 1);
    }

    /**
     * @param int $count
     * @return Skip<T>
     */
    public function skip(int $count): Skip
    {
        return new Skip($this, $count);
    }

    /**
     * @param callable(T): bool $callback
     * @return SkipWhile<T>
     */
    public function skipWhile(callable $callback): SkipWhile
    {
        return new SkipWhile($this, $callback);
    }

    /**
     * @param callable(T): bool $callback
     * @return bool
     */
    public function all(callable $callback): bool
    {
        return $this->find(fn ($val) => !$callback($val))->isNone();
    }

    /**
     * @param callable(T): bool $callback
     * @return bool
     */
    public function any(callable $callback): bool
    {
        return $this->find($callback)->isSome();
    }


    /**
     * @param iterable<T> $iter
     * @return Chain<T>
     */
    public function chain(iterable $iter): Chain
    {
        return new Chain(
            $this,
            Iterator::from($iter)
        );
    }

    /**
     * @return T
     */
    public function current(): mixed
    {
        return $this->iter->current();
    }

    public function next(): void
    {
        $this->iter->next();
    }

    public function key(): string|int|null
    {
        return $this->iter->key();
    }

    public function valid(): bool
    {
        return $this->iter->valid();
    }

    public function rewind(): void
    {
        $this->iter->rewind();
    }

    public function inspect(callable $callback): Iter
    {
        $callback($this->current());

        return $this;
    }
}