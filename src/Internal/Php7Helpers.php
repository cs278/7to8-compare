<?php

declare(strict_types=1);

namespace Cs278\Comparator\Internal;

final class Php7Helpers
{
    public static function isNumeric($input, &$value = null): bool
    {
        if (\is_float($input) || \is_int($input)) {
            $value = $input;

            return true;
        }

        if (!\is_string($input)) {
            return false;
        }
        
        if ($input === '') {
            return false;
        }
        
        $trimmedInput = ltrim($input, " \t\n\r\v\f");

        if ($trimmedInput === '') {
            return false;
        }

        if (preg_match('{^[ \t\n\r\v\f]*(?<sign>[-+])?(?<i>[0-9]+|)(?<dec>\.[0-9]+)?(?<exp>[eE][+-]?[0-9]+)?}', $trimmedInput, $m) > 0) {
            $hasTrash = strlen($trimmedInput) > strlen($m[0]);

            if ($hasTrash && $m['i'] === '') {
                return false;
            }

            $value = (int) $m['i'];

            if ((isset($m['dec']) && $m['dec'] !== '') || (isset($m['exp']) && $m['exp'] !== '')) {
                $value = (float) $value;

                if (isset($m['dec']) && $m['dec'] !== '') {
                    $dec = (float) sprintf('0.%d', (int) substr($m['dec'], 1));
                    $value += $dec;

                }

                if (isset($m['exp']) && $m['exp'] !== '') {
                    $exp = (int) substr($m['exp'], 1);
                    $value *= 10 ** $exp;
                }
            }

            $sign = $m['sign'] === '-' ? -1 : 1;

            $value *= $sign;
            
            return !$hasTrash;
        }

        return false;
    }
}