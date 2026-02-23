<?php declare(strict_types = 1);

use Contributte\Tester\Toolkit;
use Contributte\Tester\Utils\ContainerBuilder;
use Contributte\Tracy\DI\LoggerExtension;
use Contributte\Tracy\Logger\MultiLogger;
use Nette\DI\Compiler;
use Tester\Assert;
use Tests\Fixtures\Logger\SpyLogger;
use Tracy\Debugger;
use Tracy\ILogger;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(static function (): void {
	SpyLogger::$logs = [];

	$container = ContainerBuilder::of()
		->withCompiler(static function (Compiler $compiler): void {
			$compiler->addConfig([
				'services' => [
					'tracy.logger' => SpyLogger::class,
				],
			]);

			$compiler->addExtension('logger', new LoggerExtension());
		})
		->build();

	$container->initialize();

	Assert::type(MultiLogger::class, Debugger::getLogger());

	Debugger::log('foo');
	Assert::count(1, SpyLogger::$logs);
	Assert::same('foo', SpyLogger::$logs[0]['value']);
	Assert::same(ILogger::INFO, SpyLogger::$logs[0]['level']);
});
