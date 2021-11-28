<?php
declare(strict_types=1);

namespace PhpRs\Iterator;

use PhpRs\Iterator;

/**
 * @template T
 */
class Chain extends Iterator
{
    protected \Iterator $iter;
    private bool $done = false;

    /**
     * @param Iter<T> $iter
     * @param iterable<T> $chained
     */
    public function __construct(
        private Iter $initial,
        private \Iterator $chained,
    ) {
        parent::__construct($initial);
        $this->iter = $initial;
    }

    public function valid(): bool
    {
        if ($this->done) {
            return false;
        }

        if (!$this->iter->valid()) {
            if ($this->iter === $this->initial) {
                $this->iter = $this->chained;
            } else {
                $this->done = true;
                return false;
            }
        }

        return $this->iter->valid();
    }
}