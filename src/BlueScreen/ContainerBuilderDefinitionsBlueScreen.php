<?php

namespace Contributte\Tracy\BlueScreen;

use Exception;
use Nette\DI\ContainerBuilder;
use Nette\DI\ServiceCreationException;
use Tracy\Dumper;
use Tracy\Helpers;

class ContainerBuilderDefinitionsBlueScreen
{

	/** @var ContainerBuilder */
	private $builder;

	/**
	 * @param ContainerBuilder $builder
	 */
	public function __construct(ContainerBuilder $builder)
	{
		$this->builder = $builder;
	}

	/**
	 * @param Exception $e
	 * @return array|null
	 */
	public function __invoke($e)
	{
		if (!$e) return NULL;
		if (!($e instanceof ServiceCreationException)) return NULL;
		if (!($trace = Helpers::findTrace($e->getTrace(), 'Nette\DI\Compiler::compile'))) return NULL;

		$parts = [];

		// Single definition
		preg_match("#Class .+ used in service '([a-zA-Z0-9_]+)' not found.#", $e->getMessage(), $matches);
		if ($matches) {
			list ($all, $serviceName) = $matches;
			$parts[] = sprintf(
				"<div><h3>Definition for '%s'</h3>%s</div>",
				$serviceName,
				Dumper::toHtml($this->builder->getDefinition($serviceName), [Dumper::LIVE => TRUE, Dumper::COLLAPSE => FALSE])
			);
		}

		// All definitions
		$parts[] = sprintf('<div><h3>All definitions</h3>%s</div>', Dumper::toHtml($this->builder->getDefinitions(), [Dumper::LIVE => TRUE, Dumper::COLLAPSE => FALSE]));

		return [
			'tab' => 'ContainerBuilder - definitions',
			'panel' => implode('', $parts),
		];
	}

}
