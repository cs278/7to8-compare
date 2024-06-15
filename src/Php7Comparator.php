<?php

declare(strict_types=1);

namespace Cs278\Comparator;

use Cs278\Comparator\Internal\Php7Helpers;

final class Php7Comparator implements ComparatorInterface
{
    public function compare($a, $b): int
    {
        if ($a === $b) {
            return 0;
        }

        $typeA = \gettype($a);
        $typeB = \gettype($b);

        if ($typeA === 'integer' && $typeB === 'string') {
            return $this->compareNumberAndString($a, $b);
        }

        if ($typeA === 'string' && $typeB === 'integer') {
            return -$this->compareNumberAndString($b, $a);
        }

        if ($typeA === 'double' && $typeB === 'string') {
            if (\is_nan($a)) {
                return 1;
            }

            return $this->compareNumberAndString($a, $b);
        }

        if ($typeA === 'string' && $typeB === 'double') {
            if (\is_nan($b)) {
                return 1;
            }

            return -$this->compareNumberAndString($b, $a);
        }

        if ($typeA === 'string' && $typeB === 'string') {
            return $this->compareStrings($a, $b);
        }

        if ($typeA === 'object' && $typeB === 'object') {
            return $this->compareObjects($a, $b);
        }

        if ($typeA === 'array' && $typeB === 'array') {
            return $this->compareArrays($a, $b);
        }

        return $a <=> $b;
    }

    private function compareNumberAndString($a, string $b): int
    {
        if (Php7Helpers::isNumeric($b, $asNumber)) {
            return $a <=> $asNumber;
        }

        if ($a < 0 && $asNumber === null) {
            return -1;
        }

        return $a <=> $asNumber ?? $b;
    }

    private function compareStrings(string $a, string $b): int
    {
        $aIsNumber = Php7Helpers::isNumeric($a, $aAsNumber);
        $bIsNumber = Php7Helpers::isNumeric($b, $bAsNumber);

        if ($aIsNumber && $bIsNumber) {
            return $this->compare($aAsNumber, $bAsNumber);
        }
    
        return self::boundResult(strcmp($a, $b));
    }

    private function compareArrays(array $a, array $b): int
    {
        if (\count($a) !== \count($b)) {
            return \count($a) <=> \count($b);
        }

        if (($a <=> $b) !== 0) {
            foreach ($a as $aK => $aV) {
                if (isset($b[$aK])) {
                    if (0 !== $res = $this->compare($aV, $b[$aK])) {
                        return $res;
                    }
                } else {
                    return 1;
                }
            }

            return 0;
        }

        return $a <=> $b;
    }

    private function compareObjects(object $a, object $b): int
    {
        // I cannot for the life of me work out how PHP is comparing objects which
        // are of different classes, it's not by name, object ID or object hash.

        // In this case just let PHP handle it, this seems to be fine as the
        // class name appears to be checked first but I don't know how exactly it
        // is being compared.
        if (get_class($a) !== get_class($b)) {
            return $a <=> $b;
        }

        if ($this->isObjectComparableAsArray($a)) {
            return $this->compare(\get_object_vars($a), \get_object_vars($b));
        }

        return $a <=> $b;
    }

    private static function boundResult(int $result): int
    {
        return min(max($result, -1), 1);
    }

    private static function isObjectComparableAsArray(object $object): bool
    {
        return $object instanceof \stdClass || !(new \ReflectionObject($object))->isInternal();
    }
}