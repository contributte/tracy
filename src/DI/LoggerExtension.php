<?php declare(strict_types = 1);

namespace Contributte\Tracy\DI;

use Contributte\Tracy\Logger\MultiLogger;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\PhpGenerator\ClassType;
use Tracy\Debugger;

class LoggerExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$this->getContainerBuilder()
			->addDefinition($this->prefix('logger'))
			->setFactory(MultiLogger::class);
	}

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$tracyLogger = $builder->getDefinition('tracy.logger');
		assert($tracyLogger instanceof ServiceDefinition);
		$tracyLogger->setAutowired(false);

		$multiLogger = $builder->getDefinition($this->prefix('logger'));
		assert($multiLogger instanceof ServiceDefinition);
		$multiLogger->addSetup('addLogger', [$tracyLogger]);
	}

	public function afterCompile(ClassType $class): void
	{
		$class->getMethod('initialize')
			->addBody('?::setLogger($this->getService(?));', [Debugger::class, $this->prefix('logger')]);
	}

}
