<?php declare(strict_types = 1);

namespace Contributte\Tracy\BlueScreen;

use Contributte\Tracy\Sanitizer\SanitizedTrace;
use Throwable;

class SanitizedTraceBlueScreen
{

	/**
	 * @return array{tab: string, panel: string}|null
	 */
	public function __invoke(?Throwable $e): ?array
	{
		if ($e === null) {
			return null;
		}

		$markdown = SanitizedTrace::generate($e);

		return [
			'tab' => 'Sanitized Trace',
			'panel' => '<pre>' . htmlspecialchars($markdown, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</pre>',
		];
	}

}
