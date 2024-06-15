<?php

namespace Cs278\Comparator;

use PHPUnit\Framework\TestCase;

/**
 * @copyright 2024 Chris Smith
 * @license MIT
 * 
 * @covers \Cs278\Comparator\Php7Comparator
 * @uses \Cs278\Comparator\Internal\Php7Helpers
 */
final class Php7ComparatorTest extends TestCase
{
    /** @dataProvider dataCompare */
    public function testCompare(int $expected, ?array $expectedError, $a, $b): void
    {
        $comparator = new Php7Comparator();

        if ($expectedError !== null) {
            \error_clear_last();

            @$comparator->compare($a, $b);

            $error = \error_get_last();
            unset($error['line']);
            unset($error['file']);

            // Permit change to error message for now.
            $error['message'] = str_replace('stdClass@anonymous', 'class@anonymous', $error['message']);

            self::assertSame($expectedError, $error);
            return;
        }

        \error_clear_last();
        self::assertSame($expected, $comparator->compare($a, $b));
        self::assertNull(error_get_last());
    }


    public function dataCompare(): iterable
    {
        yield from require __DIR__.'/../74.php';
    }
}