<?php declare(strict_types = 1);

use Contributte\Tester\Toolkit;
use Contributte\Tracy\SanitizedDumper;
use Tester\Assert;
use Tracy\Debugger;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(static function (): void {
	$dumper = new SanitizedDumper();
	Debugger::$keysToHide[] = 'secret_tree';
	Debugger::getBlueScreen()->keysToHide[] = 'token';

	$payload = [
		'secret_tree' => ['apiKey' => 'super-secret-key'],
		'nested' => [
			'token' => ['value' => 'abc-123'],
			'profile' => [
				'email' => 'customer@example.com',
				'active' => true,
				'flags' => [1, 2, 3],
			],
		],
		'nothing' => null,
		'price' => 1250,
		'callback' => static fn (): string => 'secret',
		'request' => new stdClass(),
	];
	$payload['self'] = [];
	$payload['self']['parent'] = &$payload;

	$sanitized = $dumper->sanitize($payload);

	Assert::same('array(1)', $sanitized['secret_tree']);
	Assert::same('array(1)', $sanitized['nested']['token']);
	Assert::same('string(20)', $sanitized['nested']['profile']['email']);
	Assert::same('bool', $sanitized['nested']['profile']['active']);
	Assert::same('int(4)', $sanitized['price']);
	Assert::same('null', $sanitized['nothing']);
	Assert::same('Closure', $sanitized['callback']);
	Assert::same('object(stdClass)', $sanitized['request']);
	Assert::same('*RECURSION* array', $sanitized['self']['parent']);
});
