<?php

declare(strict_types=1);

namespace unit\Iterator;

use PhpRs\Iterator;
use PhpRs\Option;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase
{
    /** @test */
    public function simple_next()
    {
        $iter = Iterator::from([1, 2, 3, 4, 5, 6])
            ->filter(IteratorUtil::filterEvenNumbers());

        self::assertEquals(2, $iter->getNext()->unwrap());
        self::assertEquals(4, $iter->getNext()->unwrap());
        self::assertEquals(6, $iter->getNext()->unwrap());
    }

    /** @test */
    public function simple_foreach()
    {
        $iter = Iterator::from([1, 2, 3, 4, 5, 6])
            ->filter(IteratorUtil::filterEvenNumbers());
        $expected = [1 => 2, 3 => 4, 5 => 6];
        IteratorUtil::testValidForeachValues($expected, $iter);
    }

    /** @test */
    public function mapped_filter()
    {
        $iter = Iterator::from([1, 30, 5, 22, 23])
            ->map(fn(int $val) => (string)$val)
            ->filter(fn(string $x) => strlen($x) > 1);
        $expected = [1 => "30", 3 => 22, 4 => 23];
        IteratorUtil::testValidForeachValues($expected, $iter);
    }

    /** @test */
    public function multi_layer_filter(): void
    {
        $iter = Iterator::from(["1", "30", "5", "22", "23"])
            ->filter(fn(string $x) => strlen($x) > 1)
            ->filter(fn(string $x) => $x !== "30");
        $expected = [3 => 22, 4 => 23];
        IteratorUtil::testValidForeachValues($expected, $iter);
    }

    /** @test */
    public function multi_mapped_filter(): void
    {
        $iter = Iterator::from([1, 30, 5, 22, 23])
            ->map(fn(int $val) => (string) $val)
            ->filter(fn(string $x) => strlen($x) > 1)
            ->map(fn(string $x) => (int) $x)
            ->filter(IteratorUtil::filterEvenNumbers())
            ->map(fn(int $x) => (string) $x)
            ->filter(fn(string $x) => $x !== "30");
        $expected = [3 => 22];
        IteratorUtil::testValidForeachValues($expected, $iter);
    }
}