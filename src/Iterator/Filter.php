<?php

declare(strict_types=1);

namespace PhpRs\Iterator;

use PhpRs\Iterator;

/**
 * @template T
 */
class Filter extends Iterator
{
    /**
     * @var callable(T): bool $callback
     */
    private $callback;

    /**
     * @param \Iterator $iter
     * @param callable $callback
     */
    public function __construct(
        protected \Iterator $iter,
        callable $callback
    ) {
        parent::__construct($iter);
        $this->callback = $callback;
    }

    /**
     * Finds the next element before returning valid check.
     *
     * If there is a Repeat further up the iterator chain and the callback returns false this call will loop forever.
     * Just like rust :)
     *
     * @return bool
     */
    public function valid(): bool
    {
        while ($this->iter->valid() && !call_user_func($this->callback, ($this->current()))) {
            $this->next();
        }

        return $this->iter->valid();
    }
}