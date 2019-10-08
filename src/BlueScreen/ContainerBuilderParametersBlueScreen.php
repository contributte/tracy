<?php declare(strict_types = 1);

namespace Contributte\Tracy\BlueScreen;

use Nette\DI\ContainerBuilder;
use Nette\InvalidArgumentException;
use Throwable;
use Tracy\Dumper;
use Tracy\Helpers;

class ContainerBuilderParametersBlueScreen
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

		if (!($e instanceof InvalidArgumentException)) {
			return null;
		}

		if (Helpers::findTrace($e->getTrace(), 'Nette\DI\Compiler::compile') === null) {
			return null;
		}

		return [
			'tab' => 'ContainerBuilder - parameters',
			'panel' => Dumper::toHtml($this->builder->parameters, [Dumper::LIVE => true, Dumper::COLLAPSE => false]),
		];
	}

}
