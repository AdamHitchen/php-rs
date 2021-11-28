<?php
declare(strict_types=1);

namespace PhpRs\Iterator;

use PhpRs\Iterator;

enum OrderBy {
    case Gt;
    case Lt;
}