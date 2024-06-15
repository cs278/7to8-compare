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