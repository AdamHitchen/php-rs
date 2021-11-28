<?php

declare(strict_types=1);

namespace PhpRs\Iterator;

use PhpRs\Iterator;

/**
 * @template T
 */
class Cloned extends Iterator
{
    public function __construct(
        protected \Iterator $iter
    ) {
        parent::__construct($iter);
    }

    /**
     * @return T
     */
    public function current(): mixed
    {
        return clone $this->iter->current();
    }
}