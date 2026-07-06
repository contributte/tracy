<?php declare(strict_types = 1);

namespace Contributte\Tracy;

use ReflectionReference;
use Tracy\Debugger;

class SanitizedDumper
{

	public function summarize(mixed $value, ?string $key = null, ?string $class = null): string
	{
		if ($value instanceof \SensitiveParameterValue) {
			$value = $value->getValue();
		}

		return match (true) {
			$value === null => 'null',
			is_bool($value) => 'bool',
			is_int($value) => sprintf('int(%d)', strlen(ltrim((string) $value, '-'))),
			is_float($value) => sprintf('float(%d)', strlen((string) $value)),
			is_string($value) => sprintf('string(%d)', strlen($value)),
			is_array($value) => sprintf('array(%d)', count($value)),
			$value instanceof \Closure => 'Closure',
			is_object($value) => sprintf('object(%s)', $value::class),
			is_resource($value) => sprintf('resource(%s)', get_resource_type($value)),
			default => get_debug_type($value),
		};
	}

	/**
	 * @param array<int|string, bool> $seenArrayReferences
	 * @param list<array<int|string, mixed>> $seenArrays
	 * @return string|array<int|string, mixed>
	 */
	public function sanitize(mixed $value, ?string $key = null, ?string $class = null, int $depth = 0, array $seenArrayReferences = [], array $seenArrays = []): string|array
	{
		if ($this->isSensitive($key, $value, $class)) {
			return $this->summarize($value, $key, $class);
		}

		if (!is_array($value)) {
			return $this->summarize($value, $key, $class);
		}

		if ($depth >= Debugger::getBlueScreen()->maxDepth) {
			return $this->summarize($value, $key, $class);
		}

		$sanitized = [];
		$maxItems = Debugger::getBlueScreen()->maxItems;
		$seenArrays[] = $value;

		foreach ($value as $itemKey => $itemValue) {
			if ($maxItems > 0 && count($sanitized) >= $maxItems) {
				$sanitized['...'] = sprintf('omitted(%d)', count($value) - $maxItems);
				break;
			}

			if (is_array($itemValue)) {
				foreach ($seenArrays as $seenArray) {
					if ($itemValue === $seenArray) {
						$sanitized[$itemKey] = '*RECURSION* array';
						continue 2;
					}
				}
			}

			$referenceId = is_array($itemValue)
				? ReflectionReference::fromArrayElement($value, $itemKey)?->getId()
				: null;

			if ($referenceId !== null && isset($seenArrayReferences[$referenceId])) {
				$sanitized[$itemKey] = '*RECURSION* array';
				continue;
			}

			$nextSeenArrayReferences = $seenArrayReferences;
			if ($referenceId !== null) {
				$nextSeenArrayReferences[$referenceId] = true;
			}

			$sanitized[$itemKey] = $this->sanitize(
				$itemValue,
				is_int($itemKey) ? (string) $itemKey : $itemKey,
				null,
				$depth + 1,
				$nextSeenArrayReferences,
				$seenArrays,
			);
		}

		return $sanitized;
	}

	private function isSensitive(?string $key, mixed $value, ?string $class = null): bool
	{
		if ($value instanceof \SensitiveParameterValue) {
			return true;
		}

		if ($key === null) {
			return false;
		}

		$blueScreen = Debugger::getBlueScreen();
		$keysToHide = array_flip(array_map(strtolower(...), array_merge(Debugger::$keysToHide, $blueScreen->keysToHide)));

		return ($blueScreen->scrubber !== null && ($blueScreen->scrubber)($key, $value, $class))
			|| isset($keysToHide[strtolower($key)])
			|| ($class !== null && isset($keysToHide[strtolower($class . '::$' . $key)]));
	}

}
