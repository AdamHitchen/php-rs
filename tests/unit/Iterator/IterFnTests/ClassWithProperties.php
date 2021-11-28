<?php
declare(strict_types=1);

namespace unit\Iterator\IterFnTests;

use PhpRs\Iterator;
use PhpRs\Option;
use PHPUnit\Framework\TestCase;

class ClassWithProperties
{
    public function __construct(
        public int $x
    ) {
    }

    public function getX(): int
    {
        return $this->x;
    }
}