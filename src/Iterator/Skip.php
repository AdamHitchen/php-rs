<?php
declare(strict_types=1);

namespace PhpRs\Iterator;

use PhpRs\Iterator;

/**
 * @template T
 */
class Skip extends Iterator
{
    /**
     * @param Iter<T> $iter
     * @param int $count
     */
    public function __construct(
        protected \Iterator $iter,
        private int $count
    ) {
        parent::__construct($iter);
    }

    public function valid(): bool
    {
        while ($this->count > 0 && $this->iter->valid()) {
            $this->count--;
            $this->iter->next();
        }

        return $this->iter->valid();
    }
}