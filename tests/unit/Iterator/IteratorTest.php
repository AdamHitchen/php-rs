<?php
declare(strict_types=1);

namespace unit\Iterator;

use PhpRs\Iterator;
use PhpRs\Iterator\Iter;
use PhpRs\Iterator\Map;
use PHPUnit\Framework\TestCase;

class IteratorTest extends TestCase
{
    /** @test */
    public function test_iterator(): void
    {
        $iter = Iterator::from([1, 2, 3]);
        $one = $iter->getNext();
        $two = $iter->getNext();
        $three = $iter->getNext();

        self::assertEquals(1, $one->unwrap());
        self::assertEquals(2, $two->unwrap());
        self::assertEquals(3, $three->unwrap());
    }

    /** @test */
    public function test_iterator_foreach(): void
    {
        $from = ["x" => 1, "Z" => 2, 'test' => 5];
        $iter = Iterator::from($from);

        IteratorUtil::testValidForeachValues($from, $iter);
    }

    /** @test */
    public function find(): void
    {
        $iter = Iterator::from([1, 2, 3, 4, 5]);
        $clbk = fn (int $val): bool => $val >= 3;
        self::assertEquals(3, $iter->find($clbk)->unwrap());

        self::assertEquals(4, $iter->find($clbk)->unwrap());
        self::assertEquals(5, $iter->find($clbk)->unwrap());
    }

    /** @test */
    public function find_next(): void
    {
        $iter = Iterator::from([1, 2, 3, 4, 5]);
        $clbk = fn (int $val): bool => $val >= 3;
        self::assertEquals(3, $iter->find($clbk)->unwrap());
        $iter->getNext()->unwrap();

        self::assertEquals(5, $iter->getNext()->unwrap());
    }

    /** @test */
    public function find_filtered(): void
    {
        $iter = Iterator::from(range(1, 100))
            ->filter(IteratorUtil::filterEvenNumbers());

        self::assertEquals(6, $iter->find(fn ($val) => $val % 3 === 0)->unwrap());
        $iter->next();
        self::assertEquals(12, $iter->find(fn ($val) => $val % 3 === 0)->unwrap());
    }

    /** @test */
    public function iterator_interface_methods_work(): void
    {
        $iter = Iterator::from(range(1, 10));
        for ($i = 0; $i < 10; $i++) {
            self::assertTrue($iter->valid());
            self::assertEquals($i+1, $iter->current());
            self::assertEquals($i, $iter->key());
            $iter->next();
        }

        self::assertFalse($iter->valid());
        $iter->rewind();
        self::assertTrue($iter->valid());
        self::assertEquals(1, $iter->current());
        self::assertEquals(0, $iter->key());
    }
    
    /** @test */
    public function iterator_doesnt_unnecessarily_create_arrayiterator(): void
    {
        $getIterClass = static function (iterable $iter): string {
            $new = Iterator::from($iter);
            return get_class((new \ReflectionClass(Iterator::class))->getProperty('iter')->getValue($new));
        };
        self::assertEquals(
            \ArrayIterator::class,
            $getIterClass([])
        );
        self::assertEquals(
            \ArrayIterator::class,
            $getIterClass(new \ArrayIterator([]))
        );
        self::assertEquals(
            Iterator::class,
            $getIterClass(Iterator::from([]))
        );

        self::assertEquals(
            Map::class,
            $getIterClass(Iterator::from([])->map(fn ($x) => $x))
        );
    }

    /** @test */
    public function collect(): void
    {
        $expected = range(0, 5000);
        $this->assertEquals(
            $expected,
            Iterator::from($expected)->collect()
        );
    }

    /** @test */
    public function collect_with_filters(): void
    {
        $this->assertEquals(
            range(0, 9),
            Iterator::from(range(0, 5000))
                ->map(fn(int $val) => (string) $val)
                ->filter(fn(string $val) => strlen($val) === 1)
                ->map(fn(string $val) => (int) $val)
                ->collect()
        );
    }

    /** @test */
    public function collect_from_associative(): void
    {
        $this->assertEquals(
            range(1, 3),
            Iterator::from([
               'a' => 1,
               'b' => 2,
               'c' => 3
           ])->collect()
        );
    }

    /** @test */
    public function collect_assoc(): void
    {
        $expected = [
            'a' => 1,
            'b' => 2,
            'c' => 3
        ];
        $this->assertEquals(
            $expected,
            Iterator::from($expected)->collectAssoc()
        );
    }

    /** @test */
    public function collect_assoc_filter(): void
    {
        $expected = [
            'a' => 1,
            'c' => 3
        ];
        $this->assertEquals(
            $expected,
            Iterator::from([
               'a' => 1,
               'b' => 2,
               'c' => 3
           ])->filter(IteratorUtil::filterOddNumbers())
             ->collectAssoc()
        );
    }

    /** @test */
    public function collect_assoc_with_filters(): void
    {
        $expected = [
            'a' => '1',
            'c' => '3'
        ];
        $this->assertEquals(
            $expected,
            Iterator::from([
                   'a' => 1,
                   'b' => 2,
                   'c' => 3
               ])
                ->filter(fn(int $val) => $val !== 2)
                ->map(fn(int $val) => (string) $val)
                ->collectAssoc()
        );
    }

    /** @test */
    public function collect_assoc_from_sequential_array(): void
    {
        $expected = [
            0 => 1,
            2 => 3,
            4 => 5,
            6 => 7,
            8 => 9,
        ];
        $this->assertEquals(
            $expected,
            Iterator::from(range(1, 9))
                ->filter(IteratorUtil::filterOddNumbers())
                ->collectAssoc()
        );
    }

    /** @test */
    public function all_returns_false(): void
    {
        self::assertFalse(
            Iterator::from(range(5, 100))
                ->all(fn (int $val) => $val < 20)
        );
    }

    /** @test */
    public function all_short_circuits(): void
    {
        $iter = Iterator::from(range(5, 100));
        $iter->all(fn (int $val) => $val <= 20);
        self::assertEquals(
            22,
            $iter->getNext()->unwrap()
        );
    }

    /** @test */
    public function all_returns_true(): void
    {
        self::assertTrue(
            Iterator::from(range(5, 100))
                ->all(fn (int $val) => $val >= 5)
        );
    }

    /** @test */
    public function all_with_filter_returns_true(): void
    {
        self::assertFalse(
            Iterator::from(range(5, 100))
                ->filter(IteratorUtil::filterEvenNumbers())
                ->all(IteratorUtil::filterOddNumbers())
        );
    }

    /** @test */
    public function all_with_filter_returns_false(): void
    {
        self::assertTrue(
            Iterator::from(range(5, 100))
                ->filter(IteratorUtil::filterEvenNumbers())
                ->all(IteratorUtil::filterEvenNumbers())
        );
    }

    /** @test */
    public function any_returns_false(): void
    {
        self::assertFalse(
            Iterator::from(range(5, 100))
                ->any(fn (int $val) => $val < 5)
        );
    }

    /** @test */
    public function any_returns_true(): void
    {
        self::assertTrue(
            Iterator::from(range(5, 100))
                ->any(fn (int $val) => $val >= 5)
        );
    }

    /** @test */
    public function any_short_circuits(): void
    {
        $iter = Iterator::from(range(5, 100));
        $iter->any(fn (int $val) => $val >= 5);
        self::assertEquals(
            6,
            $iter->getNext()->unwrap()
        );
    }

    /** @test */
    public function any_with_filter_returns_false(): void
    {
        self::assertFalse(
            Iterator::from(range(5, 100))
                ->filter(IteratorUtil::filterEvenNumbers())
                ->any(IteratorUtil::filterOddNumbers())
        );
    }

    /** @test */
    public function any_with_filter_returns_true(): void
    {
        self::assertTrue(
            Iterator::from(range(5, 100))
                ->filter(IteratorUtil::filterEvenNumbers())
                ->all(IteratorUtil::filterEvenNumbers())
        );
    }
}