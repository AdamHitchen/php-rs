<?php
declare(strict_types=1);

namespace PhpRs\PanicHandler;

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
        $info = (string) $panicInfo;

        // we don't want someone to escape the exit by throwing an exception ;)
        try {
            $callback($info);
        } catch (\Throwable) {

        }

        trigger_error($info, E_USER_ERROR);
        exit(1);
    }
}