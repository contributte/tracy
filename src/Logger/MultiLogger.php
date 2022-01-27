<?php declare(strict_types = 1);

namespace Contributte\Tracy\Logger;

use Tracy\ILogger;

class MultiLogger implements ILogger
{

	/** @var ILogger[] */
	private $loggers = [];

	public function addLogger(ILogger $logger): void
	{
		$this->loggers[] = $logger;
	}

	/**
	 * @param mixed $value
	 * @param mixed $priority
	 */
	public function log($value, $priority = self::INFO): void
	{
		foreach ($this->loggers as $logger) {
			$logger->log($value, $priority);
		}
	}

}
