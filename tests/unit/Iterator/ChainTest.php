<?php

declare(strict_types=1);

namespace unit\Iterator;

use PhpRs\Iterator;
use PhpRs\Option;
use PHPUnit\Framework\TestCase;

class ChainTest extends TestCase
{
    /** @test */
    public function simple_chain()
    {
        $iter = Iterator::from([1, 2, 3])->chain([4, 5, 6]);

        self::assertEquals([1, 2, 3, 4, 5, 6], $iter->collect());
    }

    /** @test */
    public function chain_with_filter()
    {
        $iter = Iterator::from([1, 2, 3])
            ->chain([4, 5, 6])
            ->filter(IteratorUtil::filterOddNumbers());

        self::assertEquals([1, 3, 5,], $iter->collect());
    }

    /** @test */
    public function filter_chain()
    {
        $iter = Iterator::from([1, 2, 3])
            ->filter(IteratorUtil::filterOddNumbers())
            ->chain([4, 5, 6]);

        self::assertEquals([1, 3, 4, 5, 6,], $iter->collect());
    }
}