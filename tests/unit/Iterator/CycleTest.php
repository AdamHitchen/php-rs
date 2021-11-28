<?php

declare(strict_types=1);

namespace unit\Iterator;

use PhpRs\Iterator;
use PhpRs\Option;
use PHPUnit\Framework\TestCase;

class CycleTest extends TestCase
{
    /** @test */
    public function simple_cycle()
    {
        $iter = Iterator::from([1, 2, 3])->cycle();

        self::assertEquals(Option::Some(1), $iter->getNext());
        self::assertEquals(Option::Some(2), $iter->getNext());
        self::assertEquals(Option::Some(3), $iter->getNext());

        self::assertEquals(Option::Some(1), $iter->getNext());
        self::assertEquals(Option::Some(2), $iter->getNext());
        self::assertEquals(Option::Some(3), $iter->getNext());

        self::assertEquals(Option::Some(1), $iter->getNext());
        self::assertEquals(Option::Some(2), $iter->getNext());
        self::assertEquals(Option::Some(3), $iter->getNext());
    }

    /** @test */
    public function cycle_with_filter()
    {
        $iter = Iterator::from([1, 2, 3])->cycle()->filter(fn ($val) => $val != 2);

        self::assertEquals(Option::Some(1), $iter->getNext());
        self::assertEquals(Option::Some(3), $iter->getNext());

        self::assertEquals(Option::Some(1), $iter->getNext());
        self::assertEquals(Option::Some(3), $iter->getNext());

        self::assertEquals(Option::Some(1), $iter->getNext());
        self::assertEquals(Option::Some(3), $iter->getNext());
    }

    /** @test */
    public function filter_with_cycle()
    {
        $iter = Iterator::from([1, 2, 3])->filter(fn ($val) => $val != 2)->cycle();

        self::assertEquals(Option::Some(1), $iter->getNext());
        self::assertEquals(Option::Some(3), $iter->getNext());

        self::assertEquals(Option::Some(1), $iter->getNext());
        self::assertEquals(Option::Some(3), $iter->getNext());

        self::assertEquals(Option::Some(1), $iter->getNext());
        self::assertEquals(Option::Some(3), $iter->getNext());
    }
}