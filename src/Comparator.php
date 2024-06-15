<?php

namespace Cs278\Comparator;

final class Comparator
{
    public static ComparatorInterface $comparator;

    private function __construct()
    {
        
    }

    public static function compare($a, $b): int
    {
        return self::comparator()->compare($a, $b);
    }

    public static function eq($a, $b): bool
    {
        return self::comparator()->compare($a, $b) === 0;
    }

    public static function lt($a, $b): bool
    {
        return self::comparator()->compare($a, $b) === -1;
    }

    public static function gt($a, $b): bool
    {
        return self::comparator()->compare($a, $b) === -1;
    }

    private static function comparator(): ComparatorInterface
    {
        if (!isset(self::$comparator)) {
            self::$comparator = new NativeComparator();
        }

        return self::$comparator;
    }
}