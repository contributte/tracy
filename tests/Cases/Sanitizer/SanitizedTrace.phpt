<?php declare(strict_types = 1);

use Contributte\Tester\Toolkit;
use Contributte\Tracy\Sanitizer\SanitizedTrace;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Basic error output
Toolkit::test(static function (): void {
	$e = new RuntimeException('Something went wrong');
	$output = SanitizedTrace::generate($e, [
		'server' => [],
		'get' => [],
		'post' => [],
	]);

	Assert::contains('## Error: RuntimeException', $output);
	Assert::contains('Message: string(20)', $output);
	Assert::contains('## Stack Trace', $output);
	// Real message must not appear
	Assert::false(str_contains($output, 'Something went wrong'));
});

// Request context with HTTP data
Toolkit::test(static function (): void {
	$e = new RuntimeException('Test error');
	$output = SanitizedTrace::generate($e, [
		'server' => [
			'REQUEST_URI' => '/api/orders/123',
			'REQUEST_METHOD' => 'POST',
			'HTTP_AUTHORIZATION' => 'Bearer secret-token-abc123',
			'HTTP_CONTENT_TYPE' => 'application/json',
			'CONTENT_TYPE' => 'application/json',
		],
		'get' => [
			'page' => '1',
		],
		'post' => [
			'order_id' => 42,
			'payload' => ['item1', 'item2', 'item3'],
		],
	]);

	// Section headers present
	Assert::contains('## Request Context', $output);
	Assert::contains('URL: string(15)', $output);
	Assert::contains('Method: string(4)', $output);

	// Headers - keys preserved, values sanitized
	Assert::contains('Authorization: string(26)', $output);
	Assert::contains('Content-Type: string(16)', $output);

	// GET params - keys preserved
	Assert::contains('GET:', $output);
	Assert::contains('  page: string(1)', $output);

	// POST params - keys preserved
	Assert::contains('POST:', $output);
	Assert::contains('  order_id: int(2)', $output);
	Assert::contains('  payload: array(3)', $output);

	// Real values must not appear
	Assert::false(str_contains($output, '/api/orders/123'));
	Assert::false(str_contains($output, 'secret-token-abc123'));
	Assert::false(str_contains($output, 'Bearer'));
	Assert::false(str_contains($output, 'application/json'));
	Assert::false(str_contains($output, 'item1'));
});

// No request context in CLI mode
Toolkit::test(static function (): void {
	$e = new RuntimeException('CLI error');
	$output = SanitizedTrace::generate($e, [
		'server' => ['PHP_SELF' => 'test.php'],
		'get' => [],
		'post' => [],
	]);

	Assert::false(str_contains($output, '## Request Context'));
});

// Nested exception values are not leaked
Toolkit::test(static function (): void {
	$e = new InvalidArgumentException('User email: john@example.com is invalid');
	$output = SanitizedTrace::generate($e, [
		'server' => [],
		'get' => [],
		'post' => [],
	]);

	Assert::false(str_contains($output, 'john@example.com'));
	Assert::false(str_contains($output, 'User email'));
	Assert::contains('Message: string(39)', $output);
});
