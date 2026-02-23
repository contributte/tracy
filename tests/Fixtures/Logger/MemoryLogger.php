<?php declare(strict_types = 1);

namespace Tests\Fixtures\Logger;

use Tracy\ILogger;

class MemoryLogger implements ILogger
{

	/** @var list<array{value: mixed, level: string}> */
	public array $logs = [];

	public function log(mixed $value, string $level = self::INFO): void
	{
		$this->logs[] = [
			'value' => $value,
			'level' => $level,
		];
	}

}
