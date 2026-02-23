<?php declare(strict_types = 1);

namespace Tests\Fixtures\Logger;

use Tracy\ILogger;

class SpyLogger implements ILogger
{

	/** @var list<array{value: mixed, level: string}> */
	public static array $logs = [];

	public function log(mixed $value, string $level = self::INFO): void
	{
		self::$logs[] = [
			'value' => $value,
			'level' => $level,
		];
	}

}
