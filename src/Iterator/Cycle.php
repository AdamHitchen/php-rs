<?php
declare(strict_types=1);

namespace PhpRs\Iterator;

use PhpRs\Iterator;

/**
 * @template T
 */
class Cycle extends Iterator
{
    /**
     * @param Iter<T> $iter
     */
    public function __construct(
        protected \Iterator $iter,
    ) {
        parent::__construct($iter);
    }

    /**
     * Warning! Most cycled iterators will never run out of elements. This *WILL* result in Out of Memory issues.
     *
     * If you want to collect the iterator repeated a certain number of times - use repeat instead of cycle.
     * There are some possible scenarios where a cycled iterator may eventually end - but they would likely be better
     * implemented as a custom iterator without being cycled.
     * It's up to the user to make sure OOM errors do not occur here.
     *
     * Note: This issue also exists in rust.
     *
     * @return array
     */
    public function collect(): array
    {
        return parent::collect();
    }

    /**
     * Warning! Most cycled iterators will never run out of elements. This *WILL* result in Out of Memory issues.
     *
     * If you want to collect the iterator repeated a certain number of times - use repeat instead of cycle.
     * There are some possible scenarios where a cycled iterator may eventually end - but they would likely be better
     * implemented as a custom iterator without being cycled.
     * It's up to the user to make sure OOM errors do not occur here.
     *
     * Note: This issue also exists in rust.
     *
     * @return array
     */
    public function collectAssoc(): array
    {
        return parent::collect();
    }

    // I wonder if we could implement a cached implementation - cycleCached - could be useful for
    // iter chains with lots of processing
    // Or perhaps a more general Cached iter which does the above
    /**
     * Rewinds the iter when prior in the chain no longer returns valid
     *
     * @return bool
     */
    public function valid(): bool
    {
        if (!$this->iter->valid()) {
            $this->iter->rewind();
        }

        return $this->iter->valid();
    }
}