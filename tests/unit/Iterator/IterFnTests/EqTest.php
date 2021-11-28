<?php
declare(strict_types=1);

namespace unit\Iterator\IterFnTests;

use PhpRs\Iterator;
use PhpRs\Option;
use PHPUnit\Framework\TestCase;
use unit\Iterator\IteratorUtil;

class EqTest extends TestCase
{
    /** @test */
    public function equal_array(): void
    {
        $arr = [1,2,3,4,5,6];
        $this->assertTrue(Iterator::from($arr)->eq(Iterator::from($arr)));
    }

    /** @test */
    public function not_equal_array(): void
    {
        $iter1 = Iterator::from([1,2,3,4,5,6]);
        $iter2 = Iterator::from([6,5,4,3,2,1]);
        $this->assertFalse($iter1->eq($iter2));
    }

    /** @test */
    public function similar_array_with_different_length(): void
    {
        $iter1 = Iterator::from([1,2,3,4,5,6]);
        $iter2 = Iterator::from([1, 2, 3, 4, 5]);
        $this->assertFalse($iter1->eq($iter2));
    }

    /** @test */
    public function filtered_arrays(): void
    {
        $iter1 = Iterator::from([2, 4, 6]);
        $iter2 = Iterator::from([1, 2, 3, 4, 5, 6])->filter(IteratorUtil::filterEvenNumbers());
        $this->assertTrue($iter1->eq($iter2));
    }

    /** @test */
    public function simple_eq_by(): void
    {
        $iter1 = Iterator::from([2, 4, 6]);
        $iter2 = Iterator::from([4, 8, 12]);
        $this->assertTrue($iter1->eqBy($iter2, fn ($x, $y) => $x === $y / 2));
    }

    /** @test */
    public function simple_eq_by_false(): void
    {
        $iter1 = Iterator::from([2, 4, 6]);
        $iter2 = Iterator::from([3, 7, 999]);
        $this->assertFalse($iter1->eqBy($iter2, fn ($x, $y) => $x == $y / 2));
    }
}