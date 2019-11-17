<?php declare(strict_types = 1);

/**
 * Test: DI\NavigationPanelExtension
 */

use Contributte\Tracy\DI\NavigationPanelExtension;
use Contributte\Tracy\NavigationPanel;
use Nette\Bridges\ApplicationDI\ApplicationExtension;
use Nette\Bridges\HttpDI\HttpExtension;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;
use Tests\Fixtures\RouterFactory;
use Tracy\Bar;
use Tracy\Bridges\Nette\TracyExtension;

require_once __DIR__ . '/../../bootstrap.php';

test(function (): void {
	$loader = new ContainerLoader(TEMP_DIR, true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('application', new ApplicationExtension());
		$compiler->addExtension('http', new HttpExtension());
		$compiler->addExtension('tracy', new TracyExtension());
		$compiler->addExtension('tracy.navigation', new NavigationPanelExtension());
		$compiler->addConfig(
			[
				'services' => [RouterFactory::class . '::createRouter'],
				'tracy.navigation' => ['debug' => true],
			]
		);
	}, time());

	/** @var Container $container */
	$container = new $class();

	Assert::type(NavigationPanel::class, $container->getByType(Bar::class)->getPanel('tracy.navigation.navigation'));
});
