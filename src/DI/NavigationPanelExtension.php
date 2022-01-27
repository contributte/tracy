<?php declare(strict_types = 1);

namespace Contributte\Tracy\DI;

use Contributte\Tracy\NavigationPanel;
use Contributte\Tracy\ServiceBuilder;
use Nette\Application\IPresenterFactory;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\Presenter;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;

/**
 * @property-read stdClass $config
 */
class NavigationPanelExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'debug' => Expect::bool(false),
		]);
	}

	public function beforeCompile(): void
	{
		$builder = ServiceBuilder::of($this);

		if (!$this->config->debug) {
			return;
		}

		$definitions = $builder->findDefinitionByType(Presenter::class);
		$presenters = [];

		foreach ($definitions as $key => $presenter) {
			$presenters[$key] = $presenter->getType();
		}

		$barDef = $builder->getServiceDefinition('tracy.bar');
		$barDef
			->addSetup('addPanel', [
				new Statement(
					NavigationPanel::class,
					[$builder->getDefinitionByType(IPresenterFactory::class), $builder->getDefinitionByType(LinkGenerator::class), $presenters]
				),
				$this->prefix('navigation'),
			]);
	}

}
