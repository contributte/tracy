<?php declare(strict_types = 1);

namespace Contributte\Tracy\Logger;

use Tracy\ILogger;

class MultiLogger implements ILogger
{

	/** @var list<ILogger> */
	private array $loggers = [];

	public function addLogger(ILogger $logger): void
	{
		$this->loggers[] = $logger;
	}

	public function log(mixed $value, string $level = self::INFO): void
	{
		foreach ($this->loggers as $logger) {
			$logger->log($value, $level);
		}
	}

}
