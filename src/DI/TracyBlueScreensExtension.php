<?php declare(strict_types = 1);

namespace Contributte\Tracy\DI;

use Contributte\Tracy\BlueScreen\ContainerBuilderDefinitionsBlueScreen;
use Contributte\Tracy\BlueScreen\ContainerBuilderParametersBlueScreen;
use Nette\DI\CompilerExtension;
use Tracy\Debugger;

class TracyBlueScreensExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		Debugger::getBlueScreen()->addPanel(new ContainerBuilderParametersBlueScreen($builder));
		Debugger::getBlueScreen()->addPanel(new ContainerBuilderDefinitionsBlueScreen($builder));
	}

}
