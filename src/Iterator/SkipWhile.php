<?php
declare(strict_types=1);

namespace PhpRs\Iterator;

use PhpRs\Iterator;

/**
 * @template T
 */
class SkipWhile extends Iterator
{
    /**
     * @var callable
     */
    private $callback;
    /**
     * @var bool
     */
    private $done = false;

    public function __construct(
        protected \Iterator $iter,
        callable $callback
    ) {
        parent::__construct($iter);
        $this->callback = $callback;
    }

    public function valid(): bool
    {
        if ($this->done) {
            return $this->iter->valid();
        }

        while ($this->iter->valid() && call_user_func($this->callback, $this->current())) {
            $this->iter->next();
        }

        $this->done = true;

        return $this->iter->valid();
    }
}