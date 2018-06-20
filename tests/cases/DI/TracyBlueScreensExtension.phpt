<?php declare(strict_types = 1);

/**
 * Test: DI\TracyBlueScreensExtension
 */

use Contributte\Tracy\DI\TracyBlueScreensExtension;
use Nette\DI\Compiler;
use Nette\DI\ContainerLoader;
use Tester\Assert;
use Tracy\BlueScreen;
use Tracy\Debugger;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @return mixed[]
 */
function getPanelsCount(BlueScreen $blueScreen): array
{
	$rf = new ReflectionClass($blueScreen);
	$panelsrf = $rf->getProperty('panels');
	$panelsrf->setAccessible(true);
	$panels = $panelsrf->getValue($blueScreen);

	return $panels;
}

test(function (): void {
	Assert::count(0, getPanelsCount(Debugger::getBlueScreen()));

	$loader = new ContainerLoader(TEMP_DIR, true);
	$loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('tracy.bluescreens', new TracyBlueScreensExtension());
	}, time());

	Assert::count(2, getPanelsCount(Debugger::getBlueScreen()));
});
