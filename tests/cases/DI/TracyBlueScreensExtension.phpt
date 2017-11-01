<?php

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
 * @param BlueScreen $blueScreen
 * @return int
 */
function getPanelsCount(BlueScreen $blueScreen)
{
	$rf = new ReflectionClass($blueScreen);
	$panelsrf = $rf->getProperty('panels');
	$panelsrf->setAccessible(TRUE);
	$panels = $panelsrf->getValue($blueScreen);

	return $panels;
}

test(function () {
	Assert::count(0, getPanelsCount(Debugger::getBlueScreen()));

	$loader = new ContainerLoader(TEMP_DIR, TRUE);
	$loader->load(function (Compiler $compiler) {
		$compiler->addExtension('tracy.bluescreens', new TracyBlueScreensExtension());
	}, time());

	Assert::count(2, getPanelsCount(Debugger::getBlueScreen()));
});
