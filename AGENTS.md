# Contributte Tracy

Tracy extensions (panels, loggers, blue screens) for easier development with Nette.

## Stack

- Language: PHP >=8.2
- Framework: Tracy 2.11, plus Nette DI 3 integration
- Tests: Nette Tester; static analysis: PHPStan (level 9); code style: Contributte coding standard

## Development

```bash
make install     # install dependencies
make qa          # PHPStan and code style
make csf         # fix code style
make tests       # run all tests
vendor/bin/tester -s -p php --colors 1 -C tests/Cases/Logger/MultiLogger.phpt   # run one test file
make coverage    # code coverage
```

Run `make` to list every target.

## Principles

- KISS: write the simplest code that works; no speculative abstractions.
- DRY: one source of truth; reuse existing code before adding new.
- YAGNI: build what is needed now, not what might be needed later.
- Small, final classes with typed properties and `declare(strict_types = 1)`.
- Every change comes with a test; `make qa tests` must pass before a commit.
