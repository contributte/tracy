<?php declare(strict_types = 1);

use Contributte\Tester\Toolkit;
use Contributte\Tracy\Sanitizer\SanitizedDumper;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// String values
Toolkit::test(static function (): void {
	Assert::same('string(5)', SanitizedDumper::dump('hello'));
	Assert::same('string(0)', SanitizedDumper::dump(''));
	Assert::same('string(11)', SanitizedDumper::dump('hello world'));
});

// Integer values
Toolkit::test(static function (): void {
	Assert::same('int(1)', SanitizedDumper::dump(7));
	Assert::same('int(2)', SanitizedDumper::dump(42));
	Assert::same('int(5)', SanitizedDumper::dump(12345));
	Assert::same('int(2)', SanitizedDumper::dump(-5));
	Assert::same('int(1)', SanitizedDumper::dump(0));
});

// Float values
Toolkit::test(static function (): void {
	Assert::same('float(4)', SanitizedDumper::dump(3.14));
	Assert::same('float(3)', SanitizedDumper::dump(0.5));
});

// Null
Toolkit::test(static function (): void {
	Assert::same('null', SanitizedDumper::dump(null));
});

// Boolean values
Toolkit::test(static function (): void {
	Assert::same('bool(true)', SanitizedDumper::dump(true));
	Assert::same('bool(false)', SanitizedDumper::dump(false));
});

// Array values
Toolkit::test(static function (): void {
	Assert::same('array(3)', SanitizedDumper::dump([1, 2, 3]));
	Assert::same('array(0)', SanitizedDumper::dump([]));
	Assert::same('array(2)', SanitizedDumper::dump(['nested' => [1, 2], 'key' => 'value']));
});

// Object values
Toolkit::test(static function (): void {
	Assert::same('object(stdClass)', SanitizedDumper::dump(new stdClass()));
});

// Closure
Toolkit::test(static function (): void {
	Assert::same('Closure', SanitizedDumper::dump(static function (): void {
	}));
	Assert::same('Closure', SanitizedDumper::dump(static fn () => 1));
});

// dumpArgs
Toolkit::test(static function (): void {
	Assert::same('int(1), string(5)', SanitizedDumper::dumpArgs([7, 'hello']));
	Assert::same('', SanitizedDumper::dumpArgs([]));
	Assert::same('null, bool(true), array(2)', SanitizedDumper::dumpArgs([null, true, [1, 2]]));
});

// dumpAssoc
Toolkit::test(static function (): void {
	Assert::same(['key' => 'string(3)'], SanitizedDumper::dumpAssoc(['key' => 'abc']));
	Assert::same([], SanitizedDumper::dumpAssoc([]));
	Assert::same(
		['name' => 'string(4)', 'age' => 'int(2)'],
		SanitizedDumper::dumpAssoc(['name' => 'John', 'age' => 30]),
	);
});
