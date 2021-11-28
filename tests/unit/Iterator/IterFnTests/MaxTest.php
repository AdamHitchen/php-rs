<?php
declare(strict_types=1);

namespace unit\Iterator\IterFnTests;

use PhpRs\Iterator;
use PhpRs\Option;
use PHPUnit\Framework\TestCase;
use unit\Iterator\IteratorUtil;

class MaxTest extends TestCase
{
    /** @test */
    public function max()
    {
        $value = Iterator::from([9, 3, 5, 2, 1, 6, 32, 7, 21, 5, 712])->max();
        self::assertEquals(Option::Some(712), $value);
    }

    /** @test */
    public function max_with_none()
    {
        $value = Iterator::from([])->max();

        self::assertEquals(Option::None(), $value);
    }

    /** @test */
    public function max_with_filter()
    {
        $value = Iterator::from([9, 3, 5, 2, 1, 6, 32, 7, 21, 5, 712])
                    ->filter(fn (int $a) => $a < 50)
                    ->max();

        self::assertEquals(Option::Some(32), $value);
    }

    /** @test */
    public function max_by()
    {
        $value = Iterator::from([9, 3, 5, 2, 1, 6, 32, 7, 21, 5, 712])->maxBy(fn ($x, $y) => $x <=> $y);

        self::assertEquals(Option::Some(712), $value);
    }

    /** @test */
    public function max_by_with_filter()
    {
        $value = Iterator::from([9, 3, 5, 2, 1, 6, 32, 7, 21, 5, 712])
            ->filter(IteratorUtil::filterOddNumbers())
            ->maxBy(fn ($x, $y) => $x <=> $y);

        self::assertEquals(Option::Some(21), $value);
    }

    /** @test */
    public function max_by_empty()
    {
        $value = Iterator::from([])->maxBy(fn ($x, $y) => $x <=> $y);

        self::assertEquals(Option::None(), $value);
    }

    /** @test */
    public function max_by_key()
    {
        $greatest = Iterator::from(range(1, 9))
            ->map(fn (int $x) => new ClassWithProperties($x))
            ->maxByKey(fn (ClassWithProperties $x) => $x->getX());

        self::assertEquals(9, $greatest->unwrap()->getX());
    }

    /** @test */
    public function max_by_key_filtered()
    {
        $greatest = Iterator::from(range(1, 9))
            ->map(fn (int $x) => new ClassWithProperties($x))
            ->filter(fn (ClassWithProperties $x) => $x->getX() < 5)
            ->maxByKey(fn (ClassWithProperties $x) => $x->getX());

        self::assertEquals(4, $greatest->unwrap()->getX());
    }

    /** @test */
    public function max_by_key_returns_none()
    {
        $greatest = Iterator::from(range(1, 9))
            ->map(fn (int $x) => new ClassWithProperties($x))
            ->filter(fn (ClassWithProperties $x) => $x->getX() < 0)
            ->maxByKey(fn (ClassWithProperties $x) => $x->getX());

        self::assertEquals(Option::None(), $greatest);
    }
}