<?php
declare(strict_types=1);

namespace PhpRs\PanicHandler;

interface PanicHandlerInterface
{
    /** @internal */
    public function panic(string|\Stringable $panicInfo, callable $callback): never;
}