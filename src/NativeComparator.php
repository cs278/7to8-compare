<?php

namespace Cs278\Comparator;

final class NativeComparator implements ComparatorInterface
{
    public function compare($a, $b): int
    {
        return $a <=> $b;
    }
}