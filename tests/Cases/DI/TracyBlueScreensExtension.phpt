<?php declare(strict_types = 1);

use Contributte\Tester\Toolkit;
use Contributte\Tester\Utils\ContainerBuilder;
use Contributte\Tracy\DI\TracyBlueScreensExtension;
use Nette\DI\Compiler;
use Tester\Assert;
use Tracy\Debugger;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(static function (): void {
	$rf = new ReflectionClass(Debugger::getBlueScreen());
	$panelsrf = $rf->getProperty('panels');
	$panelsrf->setAccessible(true);

	Assert::count(0, $panelsrf->getValue(Debugger::getBlueScreen()));

	ContainerBuilder::of()
		->withCompiler(static function (Compiler $compiler): void {
			$compiler->addExtension('bluescreens', new TracyBlueScreensExtension());
		})
		->build();

	Assert::count(2, $panelsrf->getValue(Debugger::getBlueScreen()));
});
