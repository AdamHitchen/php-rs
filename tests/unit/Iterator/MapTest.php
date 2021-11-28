<?php
declare(strict_types=1);

namespace unit\Iterator;

use PhpRs\Iterator;
use PHPUnit\Framework\TestCase;

class MapTest extends TestCase
{
    /** @test */
    public function test_iterator_map() {
        $iter = Iterator::from([1, 2, 3, 4, 5, 6])
            ->filter(fn(int $val) => $val > 3);

        self::assertEquals(4, $iter->getNext()->unwrap());
        self::assertEquals(5, $iter->getNext()->unwrap());
        self::assertEquals(6, $iter->getNext()->unwrap());
    }
}