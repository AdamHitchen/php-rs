<?php

declare(strict_types=1);

namespace PhpRs\Iterator;

use PhpRs\Iterator;

/**
 * @template T
 */
class Map extends Iterator
{
    /**
     * @var callable
     */
    private $c;

    public function __construct(
        protected \Iterator $iter,
        callable $c
    ) {
        parent::__construct($iter);
        $this->c = $c;
    }

    /**
     * @return T
     */
    public function current(): mixed
    {
        $cb = $this->c;
        return $cb($this->iter->current());
    }
}