<?php

namespace Cs278\Comparator;

interface ComparatorInterface
{
    /**
     * @param mixed $a
     * @param mixed $b
     */
    public function compare($a, $b): int;

    // @todo isNumeric???
}