<?php

declare(strict_types=1);

namespace unit\Iterator;

use PhpRs\Iterator;
use PHPUnit\Framework\TestCase;

class SkipWhileTest extends TestCase
{
    /** @test */
    public function simple_skip_while()
    {
        $iter = Iterator::from([1, 2, 3, 4, 5, 6]);

        self::assertEquals([4, 5, 6], $iter->skipWhile(fn (int $val) => $val < 4)->collect());
    }

    /** @test */
    public function simple_skip_while_again()
    {
        $iter = Iterator::from(["aaaa", "aa", "aaaaa", "a"]);

        self::assertEquals(["aaaa", "aa", "aaaaa", "a"], $iter->skipWhile(fn (string $val) => strlen($val) < 2)->collect());
    }

    /** @test */
    public function skip_while_with_filter()
    {
        $expected = [6, 8, 10, 12];
        $value = Iterator::from(range(1, 12))
            ->filter(IteratorUtil::filterEvenNumbers())
            ->skipWhile(fn ($val) => $val % 3 !== 0)
            ->collect();
        self::assertEquals($expected, $value);
    }

    /** @test */
    public function filter_skip_while_filter_skip()
    {
        $expected = [75, 85, 95];
        $value = Iterator::from(range(1, 100))
            ->filter(IteratorUtil::filterOddNumbers())
            ->skipWhile(fn (int $val) => $val < 25)
            ->filter(fn (int $val) => $val % 5 === 0)
            ->skip(5)
            ->collect();
        self::assertEquals($expected, $value);
    }
}