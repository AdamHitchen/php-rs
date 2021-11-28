<?php
declare(strict_types=1);

namespace PhpRs\Iterator;

use PhpRs\Option;

/**
 * @template T
 */
interface Iter extends \Iterator
{
    /**
     * @template U
     * @param callable(T): U $c
     * @return Map<U>
     */
    public function map(callable $c): Map;

    /**
     * Filter values in the iterator
     *
     * Takes a closure to determine if an element should be yielded.
     * When the closure returns true the element will be yielded, when false is returned it will be skipped.
     *
     * ```php
     *  $iter = Iterator::from([1, 2, 3])
     *     ->filter(fn (int $value) => $value !== 2);
     *
     *  assert($iter->getNext()->unwrap() === 1);
     *  assert($iter->getNext()->unwrap() === 3);
     * ```
     *
     * @param callable(T): bool $callback
     * @return Filter<T>
     */
    public function filter(callable $callback): Filter;

    /**
     * @param callable(T): bool $c
     * @return Option<T>
     */
    public function find(callable $c): Option;
    /**
     * @return Option<T>
     */
    public function getNext(): Option;

    public function skip(int $count): Skip;
    public function skipWhile(callable $callback): SkipWhile;

    /**
     * Perform an action on each element without modifying the iterator
     *
     * Performs an action on each element. The inspect method itself does not modify the iterator in any way, however
     * the provided callback could have side effects when working with values passed by reference.
     *
     * @param callable(T) $callback
     * @return self<T>
     */
    public function inspect(callable $callback): self;
}