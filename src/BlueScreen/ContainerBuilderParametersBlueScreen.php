<?php

namespace Contributte\Tracy\BlueScreen;

use Exception;
use Nette\DI\ContainerBuilder;
use Nette\InvalidArgumentException;
use Tracy\Dumper;
use Tracy\Helpers;

class ContainerBuilderParametersBlueScreen
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
		if (!($e instanceof InvalidArgumentException)) return NULL;
		if (!($trace = Helpers::findTrace($e->getTrace(), 'Nette\DI\Compiler::compile'))) return NULL;

		return [
			'tab' => 'ContainerBuilder - parameters',
			'panel' => Dumper::toHtml($this->builder->parameters, [Dumper::LIVE => TRUE, Dumper::COLLAPSE => FALSE]),
		];
	}

}
