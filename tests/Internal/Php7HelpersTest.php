<?php

declare(strict_types=1);

namespace Cs278\Comparator\Internal;

use PHPUnit\Framework\TestCase;

/**
 * @copyright 2024 Chris Smith
 * @license MIT
 * 
 * @covers \Cs278\Comparator\Internal\Php7Helpers
 */
final class Php7HelpersTest extends TestCase
{
    /** @dataProvider dataIsNumeric */
    public function testIsNumeric(bool $expectedResult, $expectedValue, $input): void
    {
        if ($expectedResult) {
            \assert(\is_float($expectedValue) || \is_int($expectedValue));
        }

        if (\PHP_MAJOR_VERSION === 7) {
            // On PHP7 we sanity check the tests themselves.
            $castResult = $input;
            settype($castResult, gettype($expectedValue));

            self::assertSame(\is_numeric($input), $expectedResult);

            if ($expectedValue === null || !\is_nan($expectedValue)) {
                self::assertSame($castResult, $expectedValue);
            } else {
                self::assertTrue(\is_nan($castResult));
            }
        }

        self::assertSame($expectedResult, Php7Helpers::isNumeric($input, $value));

        if ($expectedValue === null || !\is_nan($expectedValue)) {
            self::assertSame($expectedValue, $value);
        } else {
            self::assertTrue(\is_nan($value));
        }
    }

    public static function dataIsNumeric(): iterable
    {
        yield [false, null, ''];
        yield [false, null, "\t \v \f \n \r"];
        yield [true, 0, '0'];
        yield [true, 0, '0000'];
        yield [true, 0, "\v \f \t \r \n   0"];
        yield [true, 1, "\v \f \t \r \n   000001"];
        yield [false, 1, "\v \f \t \r \n   000001            \v \f \t \r \n"];
        yield [true, 42, '                42'];
        yield [true, 0.0, '0.0'];
        yield [true, 0.0, '00000.0000'];
        yield [true, 0.0, '00E00'];
        yield [true, 0.0, '00E+00'];
        yield [true, 0.0, '00E-00'];
        yield [true, 0.0, '+00E-00'];
        yield [true, 0.0, '-00E+00'];
        yield [true, NAN, NAN]; // Not a number is numeric
        yield [true, -50000.0, '-.5E5'];
        yield [true, -0.0, '-.0E5'];
        yield [false, null, '-.E5'];
        yield [true, \PHP_FLOAT_EPSILON, \PHP_FLOAT_EPSILON];
        yield [true, \PHP_FLOAT_MAX, \PHP_FLOAT_MAX];

    }
}