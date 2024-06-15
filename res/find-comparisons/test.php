<?php

$a = range(1, 20);

function bar() {
	debug_print_backtrace();
}

usort($a, 'bar');
