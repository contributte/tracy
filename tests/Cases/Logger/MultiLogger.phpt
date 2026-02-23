<?php declare(strict_types = 1);

use Contributte\Tester\Toolkit;
use Contributte\Tracy\Logger\MultiLogger;
use Tester\Assert;
use Tests\Fixtures\Logger\MemoryLogger;
use Tracy\ILogger;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(static function (): void {
	$first = new MemoryLogger();
	$second = new MemoryLogger();

	$logger = new MultiLogger();
	$logger->addLogger($first);
	$logger->addLogger($second);
	$logger->log('boom', ILogger::ERROR);

	Assert::count(1, $first->logs);
	Assert::same('boom', $first->logs[0]['value']);
	Assert::same(ILogger::ERROR, $first->logs[0]['level']);

	Assert::count(1, $second->logs);
	Assert::same('boom', $second->logs[0]['value']);
	Assert::same(ILogger::ERROR, $second->logs[0]['level']);
});
