<?php
declare(strict_types=1);

namespace PhpRs\PanicHandler;

use PhpRs\Option\None;

class PanicHandler implements PanicHandlerInterface
{
    /**
     * @param string|\Stringable $panicInfo
     * @param callable $callback
     * @return never
     * @internal
     */
    public function panic(string|\Stringable $panicInfo, callable $callback): never
    {
        // we don't want someone to escape the exit by throwing an exception ;)
        try {
            $callback((string) $panicInfo);
        } catch (\Throwable) {

        }

        exit;
    }
}