<?php
declare(strict_types=1);

namespace unit\Iterator\IterFnTests;

use PhpRs\Iterator;
use PhpRs\Option;
use PHPUnit\Framework\TestCase;

class ReduceTest extends TestCase
{
    /** @test */
    public function can_reduce()
    {
        $value = Iterator::from([9, 3, 5, 2, 1, 6, 32, 7, 21, 5, 712])
                    // equivalent to Iterator->min
                    ->reduce(fn (int $a, int $b) => min($a, $b));
        self::assertEquals(Option::Some(1), $value);
    }

    /** @test */
    public function can_reduce_with_filter()
    {
        $value = Iterator::from([9, 3, 5, 2, 1, 6, 32, 7, 21, 5, 712])
                    ->filter(fn (int $a) => $a > 5)
                    ->reduce(fn (int $a, int $b) => min($a, $b));

        self::assertEquals(Option::Some(6), $value);
    }

    /** @test */
    public function empty_iter_returns_one()
    {
        $value = Iterator::from([9, 3, 5, 2, 1, 6, 32, 7, 21, 5, 712])
                    ->filter(fn (int $a) => $a === 0)
                    ->reduce(fn (int $a, int $b) => min($a, $b));

        self::assertEquals(Option::None(), $value);
    }
}