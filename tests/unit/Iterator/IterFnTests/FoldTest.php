<?php
declare(strict_types=1);

namespace unit\Iterator\IterFnTests;

use PhpRs\Iterator;
use PhpRs\Option;
use PHPUnit\Framework\TestCase;
use unit\Iterator\IteratorUtil;

class FoldTest extends TestCase
{
    /** @test */
    public function can_fold()
    {
        $iter = Iterator::from([1, 2, 3]);
        self::assertEquals(
            6,
            $iter->fold(
                0,
                fn ($x, $y) => $x + $y
            )
        );
    }

    /** @test */
    public function basic_fold()
    {
        $iter = Iterator::from([1, 2, 3]);
        self::assertEquals(
            "123",
            $iter->fold(
                "",
                fn ($x, $y) => $x . $y
            )
        );
    }

    /** @test */
    public function fold_with_filter()
    {
        $iter = Iterator::from(range(1, 10))->filter(IteratorUtil::filterOddNumbers());
        self::assertEquals(
            "13579",
            $iter->fold(
                "",
                fn ($x, $y) => $x . $y
            )
        );
    }

    /** @test */
    public function fold_initial_value_is_used()
    {
        $iter = Iterator::from(range(1, 10))->filter(IteratorUtil::filterOddNumbers());
        $reduced = $iter->fold(
            "blahblah",
            fn (string $x, int $y): string => $x . $y,
        );

        self::assertEquals(
            "blahblah13579",
            $reduced,
            'Failed asserting that Fold uses initial value'
        );
    }
}