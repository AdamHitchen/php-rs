<?php
declare(strict_types=1);

namespace unit\Iterator\IterFnTests;

use PhpRs\Iterator;
use PhpRs\Option;
use PHPUnit\Framework\TestCase;

class CountTest extends TestCase
{
    /** @test */
    public function count_basic_iter()
    {
        self::assertEquals(
            5,
            Iterator::from([5, 4, 3, 2, 1])->count()
        );
    }

    /** @test */
    public function count_filtered_iter()
    {
        self::assertEquals(
            2,
            Iterator::from([5, 4, 3, 2, 1])
                ->filter(fn ($val) => $val <= 2)
                ->count()
        );
    }

    /** @test */
    public function count_chained_iters()
    {
        $iter = Iterator::from([1, 2, 3])
            ->chain(Iterator::from([4, 5, 6]));

        self::assertEquals(
            6,
            $iter->count()
        );
    }
}