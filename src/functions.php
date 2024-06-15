<?php

namespace Cs278\Comparator;

function in_array($needle, array $haystack, bool $strict = false): bool
{
    if ($strict) {
        return \in_array($needle, $haystack, true);
    }

    return array_search($needle, $haystack, false) !== false;
}

function array_search($needle, array $haystack, bool $strict = false)
{
    if ($strict === true) {
        return \array_search($needle, $haystack, true);
    }

    foreach ($haystack as $key => $candidate) {
        if (Comparator::eq($candidate, $needle)) {
            return $key;
        }
    }

    return false;
}

function array_keys(array $array, $filterByValue = null, bool $strict = false)
{
    if (\func_num_args() === 1) {
        return \array_keys($array);
    }

    if ($strict) {
        return \array_keys($array, $filterByValue, true);
    }

    $result = [];

    foreach ($array as $key => $value) {
        if (Comparator::eq($value, $filterByValue)) {
            $result[] = $key;
        }
    }

    return $result;
}

function sort(&$array, int $flags = \SORT_REGULAR)
{
    if ($flags === \SORT_REGULAR) {
        return \usort($array, static function ($a, $b): int {
            return Comparator::compare($a, $b);
        });
    }

    return \sort($array, $flags);
}

function rsort(&$array, int $flags = \SORT_REGULAR)
{
    if ($flags === \SORT_REGULAR) {
        return \usort($array, static function ($a, $b): int {
            return -Comparator::compare($a, $b);
        });
    }

    return \rsort($array, $flags);
}

function ksort(&$array, int $flags = \SORT_REGULAR)
{
    if ($flags === \SORT_REGULAR) {
        return \uksort($array, static function ($a, $b): int {
            return Comparator::compare($a, $b);
        });
    }

    return \ksort($array, $flags);
}

function krsort(&$array, int $flags = \SORT_REGULAR)
{
    if ($flags === \SORT_REGULAR) {
        return \uksort($array, static function ($a, $b): int {
            return -Comparator::compare($a, $b);
        });
    }

    return \krsort($array, $flags);
}

function asort(&$array, int $flags = \SORT_REGULAR)
{
    if ($flags === \SORT_REGULAR) {
        return \uasort($array, static function ($a, $b): int {
            return Comparator::compare($a, $b);
        });
    }

    return \asort($array, $flags);
}

function arsort(&$array, int $flags = \SORT_REGULAR)
{
    if ($flags === \SORT_REGULAR) {
        return \uasort($array, static function ($a, $b): int {
            return -Comparator::compare($a, $b);
        });
    }

    return \arsort($array, $flags);
}
