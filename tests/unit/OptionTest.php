<?php

declare(strict_types=1);

namespace unit;

use PhpRs\Option;
use PhpRs\Option\None;
use PhpRs\Option\Some;
use PhpRs\PanicHandler\Panic;
use PhpRs\PanicHandler\PanicHandler;
use PHPUnit\Framework\TestCase;

class OptionTest extends TestCase
{

    public function setUp(): void
    {
        Panic::registerPanicHandler(new PanicHandlerFake());
        Panic::registerPanicCallback(function (string $error) {
            throw new \Exception($error);
        });
    }

    public function tearDown(): void
    {
        Panic::clearPanicCallback();
    }

    /** @test */
    public function option_can_be_mapped()
    {
        $option = Option::from(123);
        self::assertEquals(123, $option->unwrap());
        $option = $option->map(fn($val) => (string)$val);
        self::assertEquals("123", $option->unwrap());
    }

    /** @test */
    public function option_can_be_chain_mapped()
    {
        $option = Option::Some(123)
            ->map(fn($val) => $val + 1)
            ->map(fn($val) => $val + 20)
            ->map(fn($val) => (string)$val);
        self::assertEquals(144, $option->unwrap());
    }

    /** @test */
    public function panics_when_unwrapping_none()
    {
        Panic::registerPanicHandler(new PanicHandlerFake());
        Panic::registerPanicCallback(function (string $error) {
            throw new \Exception($error);
        });

        self::expectException(\Exception::class);
        None::instance()->unwrap();
    }

    /** @test */
    public function panics_with_error_when_expecting_none()
    {
        Panic::registerPanicHandler(new PanicHandlerFake());
        Panic::registerPanicCallback(function (string $error) {
            throw new \Exception($error);
        });

        try {
            Option::None()->expect("This is an error");
        } catch (\Exception $e) {
            $this->assertStringStartsWith("This is an error", $e->getMessage());
        }
    }

    /** @test */
    public function does_not_panic_when_calling_expect_on_some()
    {
        Panic::registerPanicHandler(new PanicHandlerFake());
        Panic::registerPanicCallback(function (string $error) {
            throw new \Exception($error);
        });

        $val = Option::Some(1234);

        self::assertEquals(1234, $val->expect("Test"));
    }

    /** @test */
    public function from_creates_some_with_value()
    {
        $option = Option::from(123);
        self::assertTrue($option->isSome());
        self::assertFalse($option->isNone());
        self::assertEquals(123, $option->unwrap());
    }

    /** @test */
    public function from_creates_none_with_null()
    {
        $option = Option::from(null);
        self::assertFalse($option->isSome());
        self::assertTrue($option->isNone());
    }

    /** @test */
    public function unwrap_or_returns_original_value_when_some()
    {
        $value = Option::from(123)->unwrap_or(321);
        self::assertEquals(123, $value);
    }

    /** @test */
    public function unwrap_or_returns_default_value_when_none()
    {
        $value = Option::from(null)->unwrap_or(321);
        $value2 = Option::None()->unwrap_or(321);
        self::assertEquals(321, $value);
        self::assertEquals(321, $value2);
    }

    /** @test */
    public function unwrap_or_works_with_chain_calls()
    {
        $value = Option::from(123)
            ->map(fn($val): int => $val + 7)
            ->map(fn($val) => (string)$val)
            ->unwrap_or("Test");

        self::assertEquals("130", $value);
    }

    /** @test */
    public function unwrap_or_works_with_chain_calls_with_None_value()
    {
        $value = Option::None()
            ->map(fn($val): int => $val + 7)
            ->map(fn($val) => (string)$val)
            ->unwrap_or("Test");

        self::assertEquals("Test", $value);
    }

    /** @test */
    public function unwrap_or_panics_with_null()
    {
        $this->expectException(\Exception::class);
        Option::None()
            ->map(fn($val): int => $val + 7)
            ->map(fn($val) => (string)$val)
            ->unwrap_or(null);
    }

    /** @test */
    public function unwrap_or_panics_with_null_2()
    {
        $this->expectException(\Exception::class);
        Option::from(123)->unwrap_or(null);
    }

    /** @test */
    public function unwrap_or_else_works_on_none()
    {
        $value = Option::None()
            ->unwrap_or_else(fn() => 123);
        self::assertEquals(123, $value);
    }

    /** @test */
    public function unwrap_or_else_returns_Some_value()
    {
        $value = Option::Some(321)
            ->unwrap_or_else(fn() => 123);
        self::assertEquals(321, $value);
    }

    /** @test */
    public function unwrap_or_else_is_not_executed_on_some()
    {
        $value = Option::Some(321)
            ->unwrap_or_else(function () {
                throw new \Exception("Oh no!");
            });
        self::assertEquals(321, $value);
    }

    /** @test */
    public function filter_on_some_returns_some()
    {
        $option = Option::Some(321)
            ->filter(fn($val) => $val > 100);
        self::assertEquals(321, $option->unwrap());
        self::assertTrue($option->isSome());
    }

    /** @test */
    public function filter_on_some_returns_none()
    {
        $option = Option::Some(321)
            ->filter(fn($val) => $val < 100);
        self::assertTrue($option->isNone());
    }

    /** @test */
    public function filter_on_none_returns_none()
    {
        $option = Option::None()
            ->filter(fn($val) => $val < 100);
        self::assertTrue($option->isNone());
    }

    /** @test */
    public function flatten_on_nested_none()
    {
        $optionOption = Option::Some(Option::None());
        $flattened = $optionOption->flatten();
        self::assertTrue($optionOption->isSome());
        self::assertFalse($flattened->isSome());
        self::assertTrue($flattened->isNone());
        self::assertInstanceOf(None::class, $flattened);
    }

    /** @test */
    public function flatten_on_nested_some()
    {
        $optionOption = Option::Some(Option::Some(123));
        $flattened = $optionOption->flatten();
        self::assertTrue($optionOption->isSome());
        self::assertTrue($flattened->isSome());
        self::assertInstanceOf(Some::class, $flattened);
        self::assertEquals(123, $flattened->unwrap());
    }

    /** @test */
    public function flatten_on_some()
    {
        $option = Option::Some(123);
        $flattened = $option->flatten();
        self::assertTrue($option->isSome());
        self::assertTrue($flattened->isSome());
        self::assertInstanceOf(Some::class, $flattened);
        self::assertEquals(123, $flattened->unwrap());
    }

    /** @test */
    public function flatten_on_none()
    {
        $option = Option::None();
        $flattened = $option->flatten();
        self::assertTrue($option->isNone());
        self::assertTrue($flattened->isNone());
        self::assertInstanceOf(None::class, $flattened);
    }

    /** @test */
    public function map_or_else_on_some()
    {
        $option = Option::Some("foo");

        self::assertTrue($option->isSome());
        self::assertEquals(3, $option->mapOr(42, fn ($v) => strlen($v)));;
    }

    /** @test */
    public function map_or_else_on_none()
    {
        $option = Option::None();

        self::assertTrue($option->isNone());
        self::assertEquals(42, $option->mapOr(42, fn ($v) => strlen($v)));;
    }

    /** @test */
    public function and_then_on_some() {
        $option = Option::Some(321)
            ->andThen(fn ($inner) => Option::Some((string) $inner));

        self::assertTrue($option->isSome());
        self::assertEquals("321", $option->unwrap());
    }

    /** @test */
    public function and_then_on_some_returning_none() {
        $option = Option::Some(321)
            ->andThen(fn ($inner) => Option::None());

        self::assertTrue($option->isNone());
    }

    /** @test */
    public function and_then_panics_with_invalid_value() {
        self::expectException(\Exception::class);
        $option = Option::Some(321)
            ->andThen(fn ($inner) => 123);
    }

    /** @test */
    public function and_then_on_none() {
        $option = Option::None()
            ->andThen(fn ($inner) => Option::Some(123123123));

        self::assertTrue($option->isNone());
    }

    /** @test */
    public function or_else_on_none_returning_none() {
        $option = Option::None()
            ->orElse(fn () => Option::None());

        self::assertTrue($option->isNone());
    }

    /** @test */
    public function or_else_on_none_returning_some() {
        $option = Option::None()
            ->orElse(fn () => Option::Some(123));

        self::assertTrue($option->isSome());
        self::assertEquals(123, $option->unwrap());
    }

    /** @test */
    public function or_else_on_some() {
        $option = Option::Some("Test")
            ->orElse(fn () => Option::Some("Wow!"));
        self::assertTrue($option->isSome());
        self::assertEquals("Test", $option->unwrap());
    }

    /** @test */
    public function or_else_on_none_panics(): void
    {
        $option = Option::None();
        $this->expectException(\Exception::class);
        /** @psalm-suppress InvalidArgument */
        $option->orElse(fn () => "test");
    }

    /** @test */
    public function can_convert_some_to_iter()
    {
        $expected = 1234;
        $iter = Option::Some($expected)->iter();
        self::assertEquals($expected, $iter->getNext()->unwrap());
        self::assertEquals(Option::None(), $iter->getNext());
    }

    /** @test */
    public function can_convert_none_to_iter()
    {
        $iter = Option::from(null)->iter();
        self::assertEquals(Option::None(), $iter->getNext());
    }
    
    /** @test */
    public function unwrap_or_else_panics_with_null() {
        $option = Option::None();
        $this->expectException(\Exception::class);
        /** @psalm-suppress InvalidArgument */
        $option->unwrap_or_else(fn () => null);
    }

    /** @test */
    public function some_panics_with_null() {
        $this->expectException(\Exception::class);
        Option::Some(null);
    }

    /** @test */
    public function replace_on_some()
    {
        /** @var Some $opt */
        $opt = Option::Some("123");
        $old = $opt->replace("321");

        self::assertEquals(
            Option::Some("321"),
            $opt
        );

        self::assertEquals(
            Option::Some("123"),
            $old
        );
    }

    /** @test */
    public function none_strictly_equals_none()
    {
        self::assertTrue(Option::None() === Option::None());
    }

    /** @test */
    public function none_loosely_equals_none()
    {
        self::assertTrue(Option::None() == Option::None());
    }

    /** @test */
    public function option_strictly_not_equals_option()
    {
        self::assertTrue(Option::Some(123) !== Option::Some(123));
    }

    /** @test */
    public function option_equals_options()
    {
        self::assertTrue(Option::Some(123) == Option::Some(123));
    }

    /** @test */
    public function option_not_equals_option_with_different_value()
    {
        self::assertTrue(Option::Some(123) != Option::Some(321));
    }
}