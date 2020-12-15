<?php declare(strict_types = 1);

namespace Contributte\Tracy\BlueScreen;

use Nette\DI\ContainerBuilder;
use Nette\DI\ServiceCreationException;
use Throwable;
use Tracy\Dumper;
use Tracy\Helpers;

class ContainerBuilderDefinitionsBlueScreen
{

	/** @var ContainerBuilder */
	private $builder;

	public function __construct(ContainerBuilder $builder)
	{
		$this->builder = $builder;
	}

	/**
	 * @return string[]|null
	 */
	public function __invoke(?Throwable $e): ?array
	{
		if ($e === null) {
			return null;
		}

		if (!($e instanceof ServiceCreationException)) {
			return null;
		}

		if (Helpers::findTrace($e->getTrace(), 'Nette\DI\Compiler::compile') === null) {
			return null;
		}

		$parts = [];

		// Single definition
		preg_match("#Class .+ used in service '([a-zA-Z0-9_]+)' not found.#", $e->getMessage(), $matches);
		if ($matches) {
			[, $serviceName] = $matches;
			$parts[] = sprintf(
				"<div><h3>Definition for '%s'</h3>%s</div>",
				$serviceName,
				Dumper::toHtml($this->builder->getDefinition($serviceName), [Dumper::LIVE => true, Dumper::COLLAPSE => false])
			);
		}

		// All definitions
		$parts[] = sprintf('<div><h3>All definitions</h3>%s</div>', Dumper::toHtml($this->builder->getDefinitions(), [Dumper::LIVE => true, Dumper::COLLAPSE => false]));

		return [
			'tab' => 'ContainerBuilder - definitions',
			'panel' => implode('', $parts),
		];
	}

}
