<?php
declare(strict_types=1);

namespace PhpRs\Iterator;

use PhpRs\Iterator;
use PhpRs\Option;

/**
 * @template T
 */
class Repeat extends Iterator
{
    private Option $repeated;

    /**
     * @param Iter<T> $iter
     */
    public function __construct(
        protected \Iterator $iter
    ) {
        parent::__construct($iter);
        $this->repeated = Option::None();
    }

    public function next(): void {}

    public function current(): mixed
    {
        if ($this->repeated->isNone()) {
            $this->repeated = Option::from($this->iter->current());
        }

        return $this->repeated->isSome() ? $this->repeated->unwrap() : null;
    }

    public function valid(): bool
    {
        return $this->iter->valid();
    }
}