<?php declare(strict_types = 1);

namespace Contributte\Tracy\DI;

use Contributte\Tracy\NavigationPanel;
use Nette\Application\IPresenterFactory;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\Presenter;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use Nette\DI\ServiceDefinition;

class NavigationPanelExtension extends CompilerExtension
{

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$presenters = $builder->findByType(Presenter::class);
		foreach ($presenters as $key => $presenter) {
			assert($presenter instanceof ServiceDefinition);
			$presenters[$key] = $presenter->getType();
		}

		$barDefinition = $builder->getDefinition('tracy.bar');
		assert($barDefinition instanceof ServiceDefinition);
		$barDefinition
			->addSetup('addPanel', [
				new Statement(
					NavigationPanel::class,
					[$builder->getDefinitionByType(IPresenterFactory::class), $builder->getDefinitionByType(LinkGenerator::class), $presenters]
				),
				$this->prefix('navigation'),
			]);
	}

}
