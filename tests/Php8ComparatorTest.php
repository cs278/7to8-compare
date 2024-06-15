<?php

namespace Cs278\Comparator;

use PHPUnit\Framework\TestCase;

/**
 * @copyright 2024 Chris Smith
 * @license MIT
 * 
 * @covers \Cs278\Comparator\Php8Comparator
 */
final class Php8ComparatorTest extends TestCase
{
    /** @dataProvider dataCompare */
    public function testCompare(int $expected, $a, $b): void
    {
        $comparator = new Php8Comparator();

        self::assertSame($expected, $comparator->compare($a, $b));
    }


    public function dataCompare(): iterable
    {
        foreach ((function () {
            $closure = function () {};
            $fh1 = fopen('php://memory', 'rb');
            $fh2 = fopen('php://memory', 'wb');

            yield [0, '', false];
            yield [0, '', null];
            yield [-1, '', 0];
            yield [-1, '', 0.0];
            yield [0, '', ''];
            yield [0, 'a', 'a'];
            yield [-1, 'a', 'ab'];
            yield [0, ' a ', ' a '];
            yield [0, 12.0, 12.0];
            yield [0, +0, -0];
            yield [0, INF, INF];
            yield [1, INF, -INF];
            yield [-1, false, true];
            yield [0, false, false];
            yield [0, true, true];
            yield [0, null, false];
            yield [1, true, null];
            yield [0, '0.0 ', '0.0 '];
            yield [0, ' 0.0 ', ' 0.0 '];
            yield [0, ' 0.0', ' 0.0'];
            yield [0, '0.0', '0.0'];
            yield [0, '0', '0'];
            yield [0, '0 ', '0 '];
            yield [0, ' 0', ' 0'];
            yield [0, ' 0 ', ' 0 '];
            yield [0, 0, '0'];
            yield [0, 0, '0 '];
            yield [0, 0, ' 0 '];
            yield [0, 0, ' 0'];
            yield [0, 0, '0.0'];
            yield [0, 0, '0.0 '];
            yield [0, 0, ' 0.0 '];
            yield [0, 0, ' 0.0'];
            yield [1, 'INF', '-INF'];
            yield [0, '0.0', '0'];
            yield [1, 1.5, 1];
            yield [-1, -1.5, -1];
            yield [-1, -1.5, 1];
            yield [0, [], null];
            yield [0, [], false];
            yield [1, [], ''];
            yield [1, [], 'Array'];
            yield [-1, [], true];
            yield [0, [0], true];
            yield [0, [[]], true];
            yield [0, [null], true];
            yield [1, [null], ''];
            yield [0, true, (object) []];
            yield [-1, null, (object) []];
            yield [-1, false, (object) []];
            yield [-1, [], (object) []];
            yield [1, [1, 2, 4], [1, 2, 3]];
            yield [0, [' 0.0'], [' -0.0']];
            yield [0, $closure, $closure];
            yield [1, $closure, null];
            yield [0, $closure, true];
            yield '(fh1, fh1)' => [0, $fh1, $fh1];
            yield '(fh2, fh2)' => [0, $fh2, $fh2];
            yield '(fh2, fh1)' => [1, $fh2, $fh1];
        })() as $label => $testcase) {
            $label = \is_string($label) ? $label : sprintf('(%s, %s)', var_export($testcase[1], true), var_export($testcase[2], true));

            yield $label => [$testcase[0], $testcase[1], $testcase[2]];
            yield sprintf('%s [reverse]', $label) => [$testcase[0] * -1, $testcase[2], $testcase[1]];
        }

        yield '(0, NAN)' => [1, 0, NAN];
        yield '(NAN, 0)' => [1, NAN, 0];
        yield '(NAN, NAN)' => [1, NAN, NAN];
        yield '(Closure, Closure)' => [1, function () {}, function () {}];
        yield '(Closure, object [])' => [1, function () {}, (object) []];
        yield '(object[], Closure)' => [1, (object) [], function () {}];
    }
}