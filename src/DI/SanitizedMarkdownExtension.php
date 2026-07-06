<?php declare(strict_types = 1);

namespace Contributte\Tracy\DI;

use Contributte\Tracy\BlueScreen\SanitizedMarkdownBlueScreen;
use Contributte\Tracy\SanitizedDumper;
use Contributte\Tracy\SanitizedMarkdownRenderer;
use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;
use Tracy\Debugger;

class SanitizedMarkdownExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('dumper'))
			->setFactory(SanitizedDumper::class);

		$builder->addDefinition($this->prefix('renderer'))
			->setFactory(SanitizedMarkdownRenderer::class);

		$builder->addDefinition($this->prefix('blueScreen'))
			->setFactory(SanitizedMarkdownBlueScreen::class);
	}

	public function afterCompile(ClassType $class): void
	{
		$class->getMethod('initialize')
			->addBody('?::getBlueScreen()->addPanel($this->getService(?));', [Debugger::class, $this->prefix('blueScreen')]);
	}

}
