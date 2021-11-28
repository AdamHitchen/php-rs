<?php
declare(strict_types=1);

namespace unit\Iterator\IterFnTests;

use PhpRs\Iterator;
use PhpRs\Option;
use PHPUnit\Framework\TestCase;
use unit\Iterator\IteratorUtil;

class MinTest extends TestCase
{
    /** @test */
    public function min()
    {
        $value = Iterator::from([9, 3, 5, 2, 1, 6, 32, 7, 21, 5, 712])->min();
        self::assertEquals(Option::Some(1), $value);
    }

    /** @test */
    public function min_with_none()
    {
        $value = Iterator::from([])->min();

        self::assertEquals(Option::None(), $value);
    }

    /** @test */
    public function min_with_filter()
    {
        $value = Iterator::from([9, 3, 5, 2, 1, 6, 32, 7, 21, 5, 712])
                    ->filter(fn (int $a) => $a > 21)
                    ->min();

        self::assertEquals(Option::Some(32), $value);
    }

    /** @test */
    public function min_by()
    {
        $value = Iterator::from([9, 3, 5, 2, 1, 6, 32, 7, 21, 5, 712])->minBy(fn ($x, $y) => $x <=> $y);

        self::assertEquals(Option::Some(1), $value);
    }

    /** @test */
    public function min_by_with_filter()
    {
        $value = Iterator::from([9, 3, 5, 2, 1, 6, 32, 7, 21, 5, 712])
            ->filter(IteratorUtil::filterEvenNumbers())
            ->minBy(fn ($x, $y) => $x <=> $y);

        self::assertEquals(Option::Some(2), $value);
    }

    /** @test */
    public function min_by_empty()
    {
        $value = Iterator::from([])->minBy(fn ($x, $y) => $x <=> $y);

        self::assertEquals(Option::None(), $value);
    }

    /** @test */
    public function min_by_key()
    {
        $smallest = Iterator::from(range(1, 9))
            ->map(fn (int $x) => new ClassWithProperties($x))
            ->minByKey(fn (ClassWithProperties $x) => $x->getX());
        self::assertEquals(1, $smallest->unwrap()->getX());
    }

    /** @test */
    public function min_by_key_filtered()
    {
        $smallest = Iterator::from(range(1, 9))
            ->map(fn (int $x) => new ClassWithProperties($x))
            ->filter(fn (ClassWithProperties $x) => $x->getX() > 5)
            ->minByKey(fn (ClassWithProperties $x) => $x->getX());
        self::assertEquals(6, $smallest->unwrap()->getX());
    }

    /** @test */
    public function min_by_key_returns_none()
    {
        $smallest = Iterator::from(range(1, 9))
            ->map(fn (int $x) => new ClassWithProperties($x))
            ->filter(fn (ClassWithProperties $x) => $x->getX() > 9)
            ->minByKey(fn (ClassWithProperties $x) => $x->getX());
        self::assertEquals(Option::None(), $smallest);
    }
}