<?php

declare(strict_types=1);

namespace unit\Iterator;

use PhpRs\Iterator;
use PhpRs\Option;
use PHPUnit\Framework\TestCase;

class SkipTest extends TestCase
{
    /** @test */
    public function simple_skip()
    {
        $iter = Iterator::from([1, 2, 3, 4, 5, 6]);

        self::assertEquals([4, 5, 6], $iter->skip(3)->collect());
    }

    /** @test */
    public function basic_skip()
    {
        $value = Iterator::from(range(1, 5))->skip(2)->collect();
        self::assertEquals([3, 4, 5], $value);
    }

    /** @test */
    public function skip_with_filter()
    {
        $expected = [6, 8];
        $value = Iterator::from(range(1, 9))
            ->filter(IteratorUtil::filterEvenNumbers())
            ->skip(2)
            ->collect();
        self::assertEquals($expected, $value);
    }

    /** @test */
    public function filter_skip_filter_skip()
    {
        $expected = [7, 9];
        $value = Iterator::from(range(1, 12))
            ->skip(2)
            ->filter(IteratorUtil::filterOddNumbers())
            ->skip(1)
            ->filter(fn (int $val) => $val > 5 && $val < 10)
            ->collect();
        self::assertEquals($expected, $value);
    }
}