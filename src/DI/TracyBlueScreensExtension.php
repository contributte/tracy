<?php

namespace Contributte\Tracy\DI;

use Contributte\Tracy\BlueScreen\ContainerBuilderDefinitionsBlueScreen;
use Contributte\Tracy\BlueScreen\ContainerBuilderParametersBlueScreen;
use Nette\DI\CompilerExtension;
use Nette\InvalidStateException;
use Tracy\Debugger;

final class TracyBlueScreensExtension extends CompilerExtension
{

	/**
	 * Register services
	 *
	 * @return void
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		if (!class_exists(Debugger::class)) {
			throw new InvalidStateException('Tracy is required, please install her.');
		}

		Debugger::getBlueScreen()->addPanel(new ContainerBuilderParametersBlueScreen($builder));
		Debugger::getBlueScreen()->addPanel(new ContainerBuilderDefinitionsBlueScreen($builder));
	}

}
