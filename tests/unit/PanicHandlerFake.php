<?php
declare(strict_types=1);

namespace unit;

use PhpRs\PanicHandler\PanicHandlerInterface;

class PanicHandlerFake implements PanicHandlerInterface
{
    /**
     * Whenever this fake is used a handler should be used and throw an exception
     * Otherwise it will proceed to exit; and tests will not succeed
     *
     * @param string|\Stringable $panicInfo
     * @param callable $callback
     * @return never
     */
    public function panic(string|\Stringable $panicInfo, callable $callback): never
    {
        $callback((string) $panicInfo);

        exit(1);
    }
}