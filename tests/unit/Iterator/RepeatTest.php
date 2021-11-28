<?php

declare(strict_types=1);

namespace unit\Iterator;

use PhpRs\Iterator;
use PhpRs\Option;
use PHPUnit\Framework\TestCase;

class RepeatTest extends TestCase
{
    /** @test */
    public function repeat()
    {
        $iter = Iterator::from([1, 2,])->repeat();

        self::assertEquals(Option::Some(1), $iter->getNext());
        self::assertEquals(Option::Some(1), $iter->getNext());
        self::assertEquals(Option::Some(1), $iter->getNext());
        self::assertEquals(Option::Some(1), $iter->getNext());
        self::assertEquals(Option::Some(1), $iter->getNext());
        self::assertEquals(Option::Some(1), $iter->getNext());
    }

    /** @test */
    public function repeat_filter()
    {
        $iter = Iterator::from([1, 2,])->repeat()->filter(fn($val) => $val < 2);

        self::assertEquals(Option::Some(1), $iter->getNext());
        self::assertEquals(Option::Some(1), $iter->getNext());
        self::assertEquals(Option::Some(1), $iter->getNext());
        self::assertEquals(Option::Some(1), $iter->getNext());
        self::assertEquals(Option::Some(1), $iter->getNext());
        self::assertEquals(Option::Some(1), $iter->getNext());
    }
}