<?php declare(strict_types = 1);

namespace Contributte\Tracy;

use Throwable;
use Tracy\Helpers;

class SanitizedMarkdownRenderer
{

	public function __construct(private SanitizedDumper $dumper)
	{
	}

	public function render(Throwable $throwable): string
	{
		$lines = [
			sprintf('## Error: %s', get_debug_type($throwable)),
			sprintf('Message: %s', $this->dumper->summarize($throwable->getMessage())),
			sprintf('File: %s:%d', $this->formatFile($throwable->getFile()), $throwable->getLine()),
		];

		$previous = array_slice(Helpers::getExceptionChain($throwable), 1);
		if ($previous !== []) {
			$lines[] = '';
			$lines[] = '## Previous Exceptions';

			foreach ($previous as $index => $item) {
				$lines[] = sprintf('%d. %s', $index + 1, get_debug_type($item));
				$lines[] = sprintf('   Message: %s', $this->dumper->summarize($item->getMessage()));
				$lines[] = sprintf('   File: %s:%d', $this->formatFile($item->getFile()), $item->getLine());
			}
		}

		$lines[] = '';
		$lines[] = '## Stack Trace';

		foreach ($this->createFrames($throwable) as $index => $frame) {
			$lines[] = sprintf('#%d %s', $index, $this->formatLocation($frame['file'], $frame['line']));
			$lines[] = sprintf('   %s(%s)', $frame['call'], $this->formatArguments($frame['args']));
		}

		$lines[] = '';
		$lines[] = '## Request Context';

		foreach ($this->renderRequestContext() as $line) {
			$lines[] = $line;
		}

		return implode("\n", $lines);
	}

	/**
	 * @return list<array{file: ?string, line: ?int, call: string, args: list<mixed>}>
	 */
	private function createFrames(Throwable $throwable): array
	{
		$trace = $throwable->getTrace();

		if ($trace === []) {
			return [[
				'file' => $throwable->getFile(),
				'line' => $throwable->getLine(),
				'call' => '{main}',
				'args' => [],
			]];
		}

		$frames = [];

		foreach ($trace as $index => $row) {
			$args = isset($row['args'])
				? array_values($row['args'])
				: [];

			$frames[] = [
				'file' => $index === 0 ? $throwable->getFile() : ($trace[$index - 1]['file'] ?? null),
				'line' => $index === 0 ? $throwable->getLine() : ($trace[$index - 1]['line'] ?? null),
				'call' => $this->formatCall($row),
				'args' => $args,
			];
		}

		return $frames;
	}

	/**
	 * @param array<string, mixed> $frame
	 */
	private function formatCall(array $frame): string
	{
		$function = $frame['function'] ?? null;
		if (is_string($function) && str_starts_with($function, '{closure')) {
			return 'Closure';
		}

		$class = $frame['class'] ?? null;
		if (is_string($class) && is_string($function)) {
			return $class . (is_string($frame['type'] ?? null) ? $frame['type'] : '::') . $function;
		}

		return is_string($function) ? $function : '{main}';
	}

	/**
	 * @param list<mixed> $arguments
	 */
	private function formatArguments(array $arguments): string
	{
		if ($arguments === []) {
			return '';
		}

		return implode(', ', array_map(fn (mixed $argument): string => $this->dumper->summarize($argument), $arguments));
	}

	/**
	 * @return list<string>
	 */
	private function renderRequestContext(): array
	{
		$lines = [];

		if ($this->isHttpRequest()) {
			$url = $this->createHttpUrl();
			if ($url !== null) {
				$lines[] = sprintf('URL: %s', $this->dumper->summarize($url));
			}

			if (isset($_SERVER['REQUEST_METHOD'])) {
				$lines[] = sprintf('Method: %s', $this->dumper->summarize($_SERVER['REQUEST_METHOD']));
			}

			$this->appendSection($lines, 'Headers', $this->dumper->sanitize($this->extractHeaders()));
			$this->appendSection($lines, 'Query', $this->dumper->sanitize($_GET));
			$this->appendSection($lines, 'POST', $this->extractRequestBody());

			return $lines;
		}

		$command = $this->createCliCommand();
		if ($command !== null) {
			$lines[] = sprintf('Command: %s', $this->dumper->summarize($command));
		}

		$this->appendSection($lines, 'Arguments', $this->dumper->sanitize($_SERVER['argv'] ?? []));

		return $lines;
	}

	/**
	 * @param list<string> $lines
	 * @param string|array<int|string, mixed> $value
	 */
	private function appendSection(array &$lines, string $label, string|array $value): void
	{
		if (is_string($value)) {
			$lines[] = sprintf('%s: %s', $label, $value);
			return;
		}

		if ($value === []) {
			$lines[] = sprintf('%s: array(0)', $label);
			return;
		}

		$lines[] = sprintf('%s:', $label);

		foreach ($this->renderMapping($value) as $line) {
			$lines[] = $line;
		}
	}

	/**
	 * @param array<int|string, mixed> $values
	 * @return list<string>
	 */
	private function renderMapping(array $values, int $depth = 1): array
	{
		$lines = [];
		$indent = str_repeat('  ', $depth);

		foreach ($values as $key => $value) {
			if (is_array($value)) {
				if ($value === []) {
					$lines[] = sprintf('%s%s: array(0)', $indent, (string) $key);
					continue;
				}

				$lines[] = sprintf('%s%s:', $indent, (string) $key);
				array_push($lines, ...$this->renderMapping($value, $depth + 1));
				continue;
			}

			if (!is_string($value)) {
				$value = $this->dumper->summarize($value);
			}

			$lines[] = sprintf('%s%s: %s', $indent, (string) $key, $value);
		}

		return $lines;
	}

	/** @return array<string, string> */
	private function extractHeaders(): array
	{
		if (function_exists('apache_request_headers')) {
			$headers = apache_request_headers();
			ksort($headers);
			return $headers;
		}

		$headers = [];

		foreach ($_SERVER as $key => $value) {
			if (!is_string($value)) {
				continue;
			}

			if (str_starts_with($key, 'HTTP_')) {
				$headers[$this->normalizeHeaderName(substr($key, 5))] = $value;
				continue;
			}

			if (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH', 'CONTENT_MD5', 'AUTH_TYPE'], true)) {
				$headers[$this->normalizeHeaderName($key)] = $value;
			}
		}

		ksort($headers);

		return $headers;
	}

	/** @return string|array<int|string, mixed> */
	private function extractRequestBody(): string|array
	{
		if ($_POST !== []) {
			return $this->dumper->sanitize($_POST);
		}

		if (!in_array($_SERVER['REQUEST_METHOD'] ?? null, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
			return [];
		}

		$rawBody = file_get_contents('php://input');
		if (!is_string($rawBody) || $rawBody === '') {
			return [];
		}

		$decoded = json_decode($rawBody, true);
		if (json_last_error() === JSON_ERROR_NONE) {
			return is_array($decoded)
				? $this->dumper->sanitize($decoded)
				: $this->dumper->summarize($decoded);
		}

		return $this->dumper->summarize($rawBody);
	}

	private function isHttpRequest(): bool
	{
		return isset($_SERVER['REQUEST_METHOD']) || isset($_SERVER['REQUEST_URI']) || isset($_SERVER['HTTP_HOST']);
	}

	private function createHttpUrl(): ?string
	{
		if (!isset($_SERVER['REQUEST_URI']) && !isset($_SERVER['HTTP_HOST']) && !isset($_SERVER['SERVER_NAME'])) {
			return null;
		}

		$scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== '' && strcasecmp((string) $_SERVER['HTTPS'], 'off') !== 0
			? 'https://'
			: 'http://';
		$host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';

		return $scheme . $host . ($_SERVER['REQUEST_URI'] ?? '');
	}

	private function createCliCommand(): ?string
	{
		if (!isset($_SERVER['argv']) || !is_array($_SERVER['argv'])) {
			return null;
		}

		return implode(' ', array_map(static fn (mixed $value): string => is_scalar($value) ? (string) $value : get_debug_type($value), $_SERVER['argv']));
	}

	private function normalizeHeaderName(string $key): string
	{
		return str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
	}

	private function formatLocation(?string $file, ?int $line): string
	{
		if ($file === null) {
			return 'inner-code';
		}

		return sprintf('%s:%s', $this->formatFile($file), $line ?? '?');
	}

	private function formatFile(string $file): string
	{
		$normalizedFile = str_replace('\\', '/', $file);
		$cwd = getcwd();
		$normalizedCwd = $cwd !== false
			? str_replace('\\', '/', $cwd)
			: '';

		if ($normalizedCwd !== '' && str_starts_with($normalizedFile, $normalizedCwd . '/')) {
			return substr($normalizedFile, strlen($normalizedCwd) + 1);
		}

		return $normalizedFile;
	}

}
