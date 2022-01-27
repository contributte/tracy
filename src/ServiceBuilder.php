<?php declare(strict_types = 1);

namespace Contributte\Tracy;

use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\Definition;
use Nette\DI\Definitions\ServiceDefinition;

class ServiceBuilder
{

	/** @var ContainerBuilder */
	private $builder;

	private function __construct(ContainerBuilder $builder)
	{
		$this->builder = $builder;
	}


	public static function of(CompilerExtension $extension): self
	{
		return new self($extension->getContainerBuilder());
	}

	public function getServiceDefinition(string $name): ServiceDefinition
	{
		$def = $this->builder->getDefinition($name);

		assert($def instanceof ServiceDefinition);

		return $def;
	}

	public function getDefinitionByType(string $name): Definition
	{
		return $this->builder->getDefinitionByType($name);
	}

	/**
	 * @return array<string, ServiceDefinition>
	 */
	public function findServiceDefinitionByType(string $type): array
	{
		$output = [];
		$definitions = $this->builder->findByType($type);

		foreach ($definitions as $key => $definition) {
			assert($definition instanceof ServiceDefinition);
			$output[$key] = $definition;
		}

		return $output;
	}

	/**
	 * @return array<string, Definition>
	 */
	public function findDefinitionByType(string $type): array
	{
		return $this->builder->findByType($type);
	}

	public function getServiceDefinitionByType(string $name): ServiceDefinition
	{
		$def = $this->builder->getDefinitionByType($name);

		assert($def instanceof ServiceDefinition);

		return $def;
	}

}
