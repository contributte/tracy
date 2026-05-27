<?php declare(strict_types = 1);

namespace Contributte\Tracy\Sanitizer;

use Closure;

final class SanitizedDumper
{

	/**
	 * Convert any PHP value to a sanitized type(size) string.
	 */
	public static function dump(mixed $value): string
	{
		if ($value === null) {
			return 'null';
		}

		if (is_bool($value)) {
			return 'bool(' . ($value ? 'true' : 'false') . ')';
		}

		if (is_int($value)) {
			return 'int(' . strlen((string) $value) . ')';
		}

		if (is_float($value)) {
			return 'float(' . strlen((string) $value) . ')';
		}

		if (is_string($value)) {
			return 'string(' . strlen($value) . ')';
		}

		if (is_array($value)) {
			return 'array(' . count($value) . ')';
		}

		if ($value instanceof Closure) {
			return 'Closure';
		}

		if (is_object($value)) {
			return 'object(' . $value::class . ')';
		}

		if (is_resource($value)) {
			return 'resource(' . get_resource_type($value) . ')';
		}

		return 'unknown';
	}

	/**
	 * Sanitize an array of function/method arguments to a comma-separated string.
	 *
	 * @param array<int, mixed> $args
	 */
	public static function dumpArgs(array $args): string
	{
		return implode(', ', array_map(self::dump(...), $args));
	}

	/**
	 * Sanitize an associative array, preserving keys.
	 *
	 * @param array<string, mixed> $data
	 * @return array<string, string>
	 */
	public static function dumpAssoc(array $data): array
	{
		$result = [];

		foreach ($data as $key => $value) {
			$result[$key] = self::dump($value);
		}

		return $result;
	}

}
