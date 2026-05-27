<?php declare(strict_types = 1);

namespace Contributte\Tracy\Sanitizer;

use Throwable;

final class SanitizedTrace
{

	/**
	 * Generate a sanitized Markdown report from a Throwable.
	 *
	 * @param array{server?: array<string, mixed>, get?: array<string, mixed>, post?: array<string, mixed>} $requestContext
	 */
	public static function generate(Throwable $e, array $requestContext = []): string
	{
		$sections = [];
		$sections[] = self::formatError($e);
		$sections[] = self::formatTrace($e->getTrace());

		$server = $requestContext['server'] ?? [];
		$get = $requestContext['get'] ?? [];
		$post = $requestContext['post'] ?? [];

		$request = self::formatRequest($server, $get, $post);

		if ($request !== '') {
			$sections[] = $request;
		}

		return implode("\n\n", $sections) . "\n";
	}

	private static function formatError(Throwable $e): string
	{
		$class = $e::class;
		$message = SanitizedDumper::dump($e->getMessage());
		$file = ($e->getFile() !== '' ? $e->getFile() : 'unknown') . ':' . $e->getLine();

		return sprintf("## Error: %s\nMessage: %s\nFile: %s", $class, $message, $file);
	}

	/**
	 * @param array<int, array<string, mixed>> $trace
	 */
	private static function formatTrace(array $trace): string
	{
		$lines = ['## Stack Trace'];

		foreach ($trace as $i => $frame) {
			$file = isset($frame['file']) && is_string($frame['file']) ? $frame['file'] : 'unknown';
			$line = isset($frame['line']) && is_int($frame['line']) ? $frame['line'] : 0;
			$function = isset($frame['function']) && is_string($frame['function']) ? $frame['function'] : 'unknown';
			/** @var array<int, mixed> $args */
			$args = isset($frame['args']) && is_array($frame['args']) ? $frame['args'] : [];

			$call = '';

			if (isset($frame['class'], $frame['type']) && is_string($frame['class']) && is_string($frame['type'])) {
				$call = $frame['class'] . $frame['type'];
			}

			$call .= $function . '(' . SanitizedDumper::dumpArgs($args) . ')';

			$lines[] = '#' . $i . ' ' . $file . ':' . $line;
			$lines[] = '   ' . $call;
		}

		return implode("\n", $lines);
	}

	/**
	 * @param array<string, mixed> $server
	 * @param array<string, mixed> $get
	 * @param array<string, mixed> $post
	 */
	private static function formatRequest(array $server, array $get, array $post): string
	{
		$hasUrl = isset($server['REQUEST_URI']) || isset($server['REQUEST_METHOD']);

		if (!$hasUrl && $get === [] && $post === []) {
			return '';
		}

		$lines = ['## Request Context'];

		if (isset($server['REQUEST_URI'])) {
			$lines[] = 'URL: ' . SanitizedDumper::dump($server['REQUEST_URI']);
		}

		if (isset($server['REQUEST_METHOD'])) {
			$lines[] = 'Method: ' . SanitizedDumper::dump($server['REQUEST_METHOD']);
		}

		// Headers from HTTP_* server variables
		$headers = self::extractHeaders($server);

		if ($headers !== []) {
			$lines[] = 'Headers:';

			foreach ($headers as $name => $value) {
				$lines[] = '  ' . $name . ': ' . SanitizedDumper::dump($value);
			}
		}

		if ($get !== []) {
			$lines[] = 'GET:';

			foreach (SanitizedDumper::dumpAssoc($get) as $key => $value) {
				$lines[] = '  ' . $key . ': ' . $value;
			}
		}

		if ($post !== []) {
			$lines[] = 'POST:';

			foreach (SanitizedDumper::dumpAssoc($post) as $key => $value) {
				$lines[] = '  ' . $key . ': ' . $value;
			}
		}

		return implode("\n", $lines);
	}

	/**
	 * Extract HTTP headers from $_SERVER array.
	 *
	 * @param array<string, mixed> $server
	 * @return array<string, mixed>
	 */
	private static function extractHeaders(array $server): array
	{
		$headers = [];

		foreach ($server as $key => $value) {
			if (!str_starts_with($key, 'HTTP_')) {
				continue;
			}

			$name = str_replace('_', '-', substr($key, 5));
			$name = ucwords(strtolower($name), '-');
			$headers[$name] = $value;
		}

		if (isset($server['CONTENT_TYPE'])) {
			$headers['Content-Type'] = $server['CONTENT_TYPE'];
		}

		return $headers;
	}

}
