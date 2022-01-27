<?php declare(strict_types = 1);

namespace Contributte\Tracy\DI;

use Contributte\Tracy\Logger\MultiLogger;
use Contributte\Tracy\ServiceBuilder;
use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;
use Tracy\Debugger;

class LoggerExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('logger'))
			->setFactory(MultiLogger::class);
	}

	public function beforeCompile(): void
	{
		$builder = ServiceBuilder::of($this);

		$tracyLogger = $builder->getServiceDefinition('tracy.logger');
		$tracyLogger->setAutowired(false);

		$builder->getServiceDefinition($this->prefix('logger'))
			->addSetup('addLogger', [$tracyLogger]);
	}

	public function afterCompile(ClassType $class): void
	{
		$initialize = $class->getMethod('initialize');
		$initialize->addBody('?::setLogger(?);', [Debugger::class, $this->prefix('@logger')]);
	}

}
