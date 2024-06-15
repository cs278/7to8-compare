<?php

require 'vendor/autoload.php';

class a {public int $prop = 3;}
class b {public int $prop = 2;}
class c {public int $prop = 1;}

class x {public int $prop = 1;}
class y extends x {}

$vars = [new b(), new c(), new a(), new c(), new a(), new b()];

sort($vars);

dump($vars);
debug_zval_dump($vars[0], $vars[1], $vars[2]);
// dump(array_map('intval', $vars));

// $vars2 = array_combine(array_map('spl_object_id', $vars), $vars);

// ksort($vars2);

// dump($vars2);

dump(new x() <=> new x());

dump(SORT_REGULAR);
dump(SORT_NUMERIC);
dump(SORT_STRING);
dump(SORT_LOCALE_STRING);
dump(SORT_NATURAL);