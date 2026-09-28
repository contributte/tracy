# Contributte Tracy

Instructions for AI coding agents working in this repository.

## Overview

`contributte/tracy` adds two DI extensions to Tracy in Nette applications. `TracyBlueScreensExtension` adds
BlueScreen panels that dump the `ContainerBuilder` parameters and service definitions when the container fails to
compile. `LoggerExtension` puts a `MultiLogger` in front of the Tracy logger so more loggers receive every message.
It is a small library, not a replacement for Tracy and not a bar panel collection.

- **PHP**: 8.2 to 8.5 (`>=8.2` in `composer.json`)
- **Package**: `contributte/tracy`, namespace `Contributte\Tracy\`
- **Extensions**: `Contributte\Tracy\DI\TracyBlueScreensExtension`, `Contributte\Tracy\DI\LoggerExtension`
- **Integrates**: `tracy/tracy` 2.11.2+; `nette/di` 3.1+ is needed for the extensions but only in `require-dev`
  (`conflict` blocks `<3.1`)

## Documentation

- `.docs/README.md` is the user documentation and the page on contributte.org. Update it in the same pull request
  when an extension or its behaviour changes.
- `DESIGN.md` describes the BlueScreen panels and the static error pages in `src/resources/`. Read it before
  changing `src/BlueScreen/` or `src/resources/`.
- Organization rules for code, tests and tooling are in
  [contributte/contributte specs](https://github.com/contributte/contributte/tree/master/specs).

## Commands

```bash
# Install dependencies
make install

# Run all checks (PHPStan level 9 + code style), does not run tests
make qa

# Fix code style
make csf

# Run all tests, or one file
make tests
vendor/bin/tester -s -p php --colors 1 -C tests/Cases/DI/LoggerExtension.phpt

# Generate code coverage (coverage.html)
make coverage
```

CI runs the tests on PHP 8.2 to 8.5 and once on PHP 8.2 with `--prefer-lowest`.

## Conventions

- Tests are Nette Tester `.phpt` files in `tests/Cases` with `Toolkit::test()`; containers are built with
  `ContainerBuilder` from `contributte/tester`. Test loggers live in `tests/Fixtures/Logger` (namespace `Tests\`).
- `mockery/mockery` is in `require-dev`, but no test uses it. Prefer small fixture classes like `SpyLogger`.

## Traps

- **BlueScreen panels are added while the container compiles.** `TracyBlueScreensExtension::loadConfiguration()`
  calls `Debugger::getBlueScreen()->addPanel()` directly. With a cached container nothing is added, which is fine,
  because the panels only matter for compile errors.
- **The panels react only to compile errors.** Each panel returns `null` unless the exception is a
  `ServiceCreationException` (definitions) or `Nette\InvalidArgumentException` (parameters) and its trace contains
  `Nette\DI\Compiler::compile`.
- **The single-definition section depends on an error message.** `ContainerBuilderDefinitionsBlueScreen` matches
  `Class ... used in service '...' not found.`; if nette/di words it differently, only "All definitions" shows.
- **Tests share Tracy's global state.** `TracyBlueScreensExtension.phpt` asserts the BlueScreen starts with 0 panels
  and `LoggerExtension.phpt` replaces `Debugger::getLogger()`. Keep each case in its own file.
- **`LoggerExtension` needs the `tracy.logger` service.** It reads it in `beforeCompile()`, so the container build
  fails without Tracy's own extension. It turns off autowiring of `tracy.logger`, so `Tracy\ILogger` resolves to
  `MultiLogger`, and `afterCompile()` calls `Debugger::setLogger()` in `initialize()`.
- **`src/resources/500.html`, `500.json` and `500.txt` are not used by any class.** `500.html` also holds two pages:
  a static one followed by a copy of Tracy's PHP error template. See `DESIGN.md` before relying on them.
- Usage, configuration and examples for users live in `.docs/README.md`, not here.
