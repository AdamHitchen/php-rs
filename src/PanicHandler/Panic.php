<?php
declare(strict_types=1);

namespace PhpRs\PanicHandler;

use PhpRs\Option;

class Panic
{
    private static ?PanicHandlerInterface $instance = null;
    private static ?Option $panicCallback = null;

    /**
     * @param callable $callback
     * @return void
     */
    public static function registerPanicCallback(callable $callback): void
    {
        self::$panicCallback = Option::Some($callback);
    }

    public static function clearPanicCallback(): void
    {
        self::$panicCallback = Option::None();
    }

    public static function registerPanicHandler(PanicHandlerInterface $handler): void
    {
        self::$instance = $handler;
    }

    /**
     * @param string|\Stringable $panicInfo
     * @return never
     * @internal
     */
    public static function panic(string|\Stringable $panicInfo): never
    {
        $callback = self::$panicCallback ?? Option::None();

        if (self::$instance === null) {
            self::$instance = new PanicHandler();
        }

        self::$instance->panic($panicInfo, $callback->unwrap_or(fn($val) => $val));
    }

    /**
     * Utility function to panic when the provided value is null
     *
     * @param mixed $value - the value to check if it is null
     * @return void
     * @internal
     */
    public static function panicIfNull(mixed $value): void
    {
        if ($value === null) {
            self::panic("Passed null value");
        }
    }
}