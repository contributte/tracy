<?php declare(strict_types = 1);

use Contributte\Tester\Toolkit;
use Contributte\Tracy\SanitizedDumper;
use Contributte\Tracy\SanitizedMarkdownRenderer;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

function processPayment(int $orderId, string $token, array $payload): void
{
	throw new RuntimeException('Payment failed for card 4111111111111111');
}

function checkout(array $request): void
{
	processPayment(123, 'tok_live_secret_123456', $request);
}

Toolkit::test(static function (): void {
	$originalServer = $_SERVER;
	$originalGet = $_GET;
	$originalPost = $_POST;
	$originalIgnoreArgs = ini_get('zend.exception_ignore_args');

	$_SERVER['REQUEST_METHOD'] = 'POST';
	$_SERVER['HTTPS'] = 'on';
	$_SERVER['HTTP_HOST'] = 'shop.example.test';
	$_SERVER['REQUEST_URI'] = '/checkout?gateway=card';
	$_SERVER['HTTP_AUTHORIZATION'] = 'Bearer top-secret-token';
	$_SERVER['CONTENT_TYPE'] = 'application/json';
	$_GET = [
		'gateway' => 'card',
	];
	$_POST = [
		'order_id' => 123,
		'payload' => [
			'customer' => [
				'email' => 'customer@example.com',
			],
			'items' => [
				1,
				2,
			],
		],
	];

	ini_set('zend.exception_ignore_args', '0');

	$renderer = new SanitizedMarkdownRenderer(new SanitizedDumper());

	try {
		checkout($_POST);
		Assert::fail('Exception was not thrown.');
	} catch (RuntimeException $e) {
		$markdown = $renderer->render($e);

		Assert::contains('## Error: RuntimeException', $markdown);
		Assert::contains('Message: string(40)', $markdown);
		Assert::contains('Method: string(4)', $markdown);
		Assert::contains('URL: string(47)', $markdown);
		Assert::contains('Authorization: string(23)', $markdown);
		Assert::contains('Content-Type: string(16)', $markdown);
		Assert::contains('Host: string(17)', $markdown);
		Assert::contains('gateway: string(4)', $markdown);
		Assert::contains('order_id: int(3)', $markdown);
		Assert::contains('email: string(20)', $markdown);
		Assert::contains('processPayment(int(3), string(22), array(2))', $markdown);
		Assert::contains('checkout(array(2))', $markdown);
		Assert::contains('Closure()', $markdown);
		Assert::notContains('4111111111111111', $markdown);
		Assert::notContains('tok_live_secret_123456', $markdown);
		Assert::notContains('Bearer top-secret-token', $markdown);
		Assert::notContains('customer@example.com', $markdown);
		Assert::notContains('/checkout?gateway=card', $markdown);
	}

	$_SERVER = $originalServer;
	$_GET = $originalGet;
	$_POST = $originalPost;

	if ($originalIgnoreArgs !== false) {
		ini_set('zend.exception_ignore_args', (string) $originalIgnoreArgs);
	}
});
