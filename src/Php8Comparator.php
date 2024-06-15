<?php

namespace Cs278\Comparator;

final class Php8Comparator implements ComparatorInterface
{
    public function compare($a, $b): int
    {
        return $a <=> $b; // @todo Implement
    }
}