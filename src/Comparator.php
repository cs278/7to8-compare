<?php

namespace Cs278\Comparator;

final class Comparator
{
    private static ComparatorInterface $default;
    private static ?ComparatorInterface $override;

    private function __construct()
    {
        
    }

    public static function initialize(?ComparatorInterface $comparator = null): void
    {
        if (isset(self::$default)) {
            throw new \BadMethodCallException('Already initialized, did you mean to call reset()?');
        }

        if ($comparator !== null) {
            self::$default = $comparator;
        } else {
            self::$default = new NativeComparator();
        }
    }

    public static function reset(): void
    {
        if (self::$override !== null) {
            throw new \BadMethodCallException('Cannot call reset() when temporary comparator is in use');
        }

        unset(self::$default);
    }

    public static function with(ComparatorInterface $comparator, \Closure $callback)
    {
        $old = self::$override;
        self::$override = $comparator;

        try {
            return $callback();
        } finally {
            self::$override = $old;
        }
    }

    public static function compare($a, $b): int
    {
        return (self::$override ?? self::$default)->compare($a, $b);
    }

    public static function eq($a, $b): bool
    {
        return (self::$override ?? self::$default)->compare($a, $b) === 0;
    }

    public static function lt($a, $b): bool
    {
        return (self::$override ?? self::$default)->compare($a, $b) === -1;
    }

    public static function gt($a, $b): bool
    {
        return (self::$override ?? self::$default)->compare($a, $b) === -1;
    }
}