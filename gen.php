<?php

// @todo Extend stdClass and compare??!?

require 'vendor/autoload.php';

interface Ref
{
    public function value();
    public function export(): string;
}

final class VarRef implements Ref
{
    /** @readonly */
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function value()
    {
        if (!isset($GLOBALS[$this->name])) {
            throw new \RuntimeException('Undefined variable '. $this->name);
        }

        return $GLOBALS[$this->name];
    }

    public function export(): string
    {
        return '$'.$this->name;
    }
}

final class ConstRef implements Ref
{
    /** @readonly */
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function value()
    {
        return constant($this->name);
    }

    public function export(): string
    {
        return $this->name;
    }
}

function value($value)
{
    if ($value instanceof Ref) {
        return $value->value();
    }

    if (\is_array($value)) {
        return array_map('value', $value);
    }

    if ($value instanceof \stdClass) {
        return (object) value(get_object_vars($value));
    }

    return $value;
}

function compare($a, $b): int
{
    $a = value($a);
    $b = value($b);

    return @($a <=> $b);
}

function export($value): string
{
    if ($value === null) {
        return 'null';
    }

    if ($value instanceof Ref) {
        return $value->export();
    }

    if ($value instanceof \stdClass) {
        return sprintf('(object) %s', export(get_object_vars($value)));
    }

    if ($value === []) {
        return '[]';
    }

    if (is_string($value)) {
        $isSimple = $value === '' || preg_match('{^[[:graph:] ]+$}', $value) > 0;

        if ($isSimple) {
            return var_export($value, true);
        }

        return sprintf('hex2bin(%s)', var_export(bin2hex($value), true));
    }

    if (\is_array($value) && array_is_list($value)) {
        $result = '[';

        foreach ($value as $innerValue) {
            $result .= export($innerValue);
            $result .= ',';
        }

        $result[strlen($result) - 1] = ']';

        return $result;
    }

    if (\is_array($value)) {
        $result = '[';

        foreach ($value as $innerKey => $innerValue) {
            $result .= export($innerKey);
            $result .= ' => ';
            $result .= export($innerValue);
            $result .= ',';
        }

        $result[strlen($result) - 1] = ']';

        return $result;
    }

    if (\is_object($value)) {
        return sprintf('unserialize(%s)', var_export(serialize($value), true));
    }

    return var_export($value, true);
}

$generator = function () {
    $arrays = [
        '[]' => [],
        '[[]]' => [[]],
        '[1 => []]' => [1 => []],
        '[1 => null]' => [1 => null],
        '[0]' => [0],
        '[0.0]' => [0.0],
        '[null]' => [null],
        '[false]' => [false],
        '[true]' => [true],
        '[\'\']' => [''],
        '[a, b, c]' => ['a', 'b', 'c'],
        '[c, b, a]' => ['c', 'b', 'a'],
        '[$fh1]' => [new VarRef('fh1')],
        '[foo => bar]' => ['foo' => 'bar'],
        '[test, \'\']' => ['test', ''],
        '[test, 0]' => ['test', 0],
        '[a => 1, b => 2, c => 3]' => ['a' => 1, 'b' => 2, 'c' => 3],
        '[b => 2, a => 1, c => 3]' => ['b' => 2, 'a' => 1, 'c' => 3],
    ];

    yield 'false' => false;
    yield 'true' => true;
    yield 'null' => null;
    yield '0' => 0;
    yield '1' => 1;
    yield '-1' => -1;
    yield '0.0' => 0.0;
    yield '-1.0' => -1.0;
    yield '1.0' => 1.0;
    yield "''" => '';
    yield "'0'" => '0';
    yield "'-0'" => '-0';
    yield "'+0'" => '+0';
    yield "'0.0'" => '0.0';
    yield "'-0.0'" => '-0.0';
    yield "'+0.0'" => '+0.0';
    yield "'1.0'" => '1.0';
    yield "'-1.0'" => '-1.0';
    yield "'+1.0'" => '+1.0';
    yield "'-1'" => '-1';
    yield "'+1'" => '+1';
    yield "'1'" => '1';

    // Spaces and 0
    yield "' 0'" => ' 0';
    yield "' -0'" => ' -0';
    yield "' +0'" => ' +0';
    yield "' 0 '" => ' 0 ';
    yield "' -0 '"  => ' -0 ';
    yield "' +0 '"  => ' +0 ';
    yield "'0 ' " => '0 ';
    yield "'-0 '"  => '-0 ';
    yield "'+0 '"  => '+0 ';

    // Spaces and 0.0
    yield "' 0.0'" => ' 0.0';
    yield "' -0.0'" => ' -0.0';
    yield "' +0.0'" => ' +0.0';
    yield "' 0.0 ' " => ' 0.0 ';
    yield "' -0.0 '"  => ' -0.0 ';
    yield "' +0.0 '"  => ' +0.0 ';
    yield "'0.0 ' " => '0.0 ';
    yield "'-0.0 '"  => '-0.0 ';
    yield "'+0.0 '"  => '+0.0 ';

    yield "'0000'" => '0000';
    yield "'+0000'" => '+0000';
    yield "-'0000'" => '-0000';

    yield "'0000.0000'" => '0000.0000';
    yield "'+0000.0000'" => '+0000.0000';
    yield "'-0000.0000'" => '-0000.0000';

    yield "'5E0'" => '5E0';
    yield "'5E5'" => '5E5';
    yield "'5E+5'" => '5E+5';
    yield "'5E-5'" => '5E-5';

    yield "'0E0'" => '0E0';
    yield "'0E5'" => '0E5';
    yield "'0E+5'" => '0E+5';
    yield "'0E-5'" => '0E-5';

    yield "'03'" => '03'; // Octal?

    yield "'2abc'" => '2abc';
    yield "'2.5abc'" => '2.5abc';
    yield "'abc2abc'" => 'abc2abc';
    yield "'abc2.5abc'" => 'abc2.5abc';

    yield '\'\0\'' => "\0";

    yield '\'\t\n\r\v\f5\t\n\r\v\f\'' => "\t\n\r\v\f5\t\n\r\v\f";
    yield '\'\0\t\n\r\v\f5\0\t\n\r\v\f\'' => "\0\t\n\r\v\f5\0\t\n\r\v\f";

    yield '12.0000000000001' => 12.0000000000001;
    yield '12.000000000000002' => 12.000000000000002;
    yield 'pi' => new ConstRef('M_PI');
    yield 'int(max)' => new ConstRef('PHP_INT_MAX');
    yield 'int(min)' => new ConstRef('PHP_INT_MIN');
    yield 'float(epsilon)' => new ConstRef('PHP_FLOAT_EPSILON');
    yield 'float(min)' => new ConstRef('PHP_FLOAT_MIN');
    yield 'float(max)' => new ConstRef('PHP_FLOAT_MAX');
    
    // var_export() has special handling for these constants.
    // https://github.com/php/php-src/blob/d545b1d64350cac9cbf27859ad44d3ba32f6b736/Zend/zend_strtod.c#L4518-L4522
    yield 'float(inf)' => INF;
    yield 'float(-inf)' => -INF;
    yield 'float(nan)' => NAN;

    yield '$closure1' => new VarRef('closure1');
    yield '$closure2' => new VarRef('closure2');
    yield '$fh1' => new VarRef('fh1');
    yield '$fh2' => new VarRef('fh2');
    yield '$proc' => new VarRef('proc');
    yield '$stdClass1' => new VarRef('stdClass1');
    yield 'DateTime(2024-01-01T12:00:00Z)' => new \DateTime('2024-01-01 12:00:00', new \DateTimeZone('UTC'));
    yield 'DateTime(2024-01-01T12:00:01Z)' => new \DateTime('2024-01-01 12:00:01', new \DateTimeZone('UTC'));
    yield 'DateTimeImmutable(2024-01-01T12:00:00Z)' => new \DateTimeImmutable('2024-01-01 12:00:00', new \DateTimeZone('UTC'));
    yield 'DateTimeImmutable(2024-01-01T12:00:01Z)' => new \DateTimeImmutable('2024-01-01 12:00:01', new \DateTimeZone('UTC'));
    yield from $arrays;

    foreach ($arrays as $label => $value) {
        yield sprintf('object%s', $label) => (object) $value;
    }
};

$vars = [
    'closure1' =>  "function () {}",
    'closure2' =>  "function () {}",
    'fh1' =>  "fopen('php://memory', 'rb')",
    'fh2' =>  "fopen('php://memory', 'wb')",
    'proc' => "proc_open(['/bin/false'], [], \$pipes)",
    'stdClass1' => <<<'EOT'
    (function () {
        $obj = new class extends stdClass {};
        $obj->my = 'data';

        return $obj;
    })()
    EOT,
];

printf('<'."?php\n");
printf("return (static function () {\n");

foreach ($vars as $name => $code) {
    $GLOBALS[$name] = eval(sprintf('return %s;', $code));

    printf('$%s = %s;', $name, $code);
    echo "\n";
}

printf("/"."** @var array<string,array{int<-1,1>, array{type: int, message: string}|null, mixed, mixed}> */\n");
printf("\$tests = [];\n");

foreach ($generator() as $l1 => $v1) {
    foreach ($generator() as $l2 => $v2) {
        error_clear_last();
        $result = compare($v1, $v2);
        $error = error_get_last();

        printf(
            "\$tests[%s] = [%d, %s, %s, %s];\n",
            export(sprintf('%s <=> %s', $l1, $l2)),
            $result,
            export($error !== null ? ['type' => $error['type'], 'message' => $error['message']] : null),
            export($v1),
            export($v2),
        );
    }
}

// var_dump(get_resource_type());

printf("return \$tests;\n");
printf("})();\n");