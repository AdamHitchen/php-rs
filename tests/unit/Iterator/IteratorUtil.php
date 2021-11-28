<?php
declare(strict_types=1);

namespace unit\Iterator;

use PhpRs\Iterator\Iter;
use PHPUnit\Framework\TestCase;

class IteratorUtil
{
    public static function testValidForeachValues(array $data, Iter $iter): void {
        $count = 0;
        foreach ($iter as $key => $value) {
            $count++;
            TestCase::assertEquals(current($data), $value);
            TestCase::assertEquals(key($data), $key);
            next($data);
        }

        TestCase::assertEquals($count, count($data));
    }

    /**
     * I seem to be writing a lot of tests with this..
     * @return callable(int): bool
     */
    public static function filterEvenNumbers(): callable
    {
        return fn (int $val) => $val % 2 === 0;
    }

    public static function filterOddNumbers(): callable
    {
        return fn (int $val) => $val % 2 === 1;
    }
}