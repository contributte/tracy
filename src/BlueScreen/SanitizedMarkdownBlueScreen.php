<?php declare(strict_types = 1);

namespace Contributte\Tracy\BlueScreen;

use Contributte\Tracy\SanitizedMarkdownRenderer;
use Throwable;
use Tracy\Helpers;

class SanitizedMarkdownBlueScreen
{

	public function __construct(private SanitizedMarkdownRenderer $renderer)
	{
	}

	/**
	 * @return array{tab: string, panel: string, collapsed: bool}|null
	 */
	public function __invoke(?Throwable $e): ?array
	{
		if ($e === null) {
			return null;
		}

		return [
			'tab' => 'Sanitized Markdown',
			'panel' => sprintf('<pre>%s</pre>', Helpers::escapeHtml($this->renderer->render($e))),
			'collapsed' => true,
		];
	}

}
