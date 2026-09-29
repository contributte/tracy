![](https://heatbadger.now.sh/github/readme/contributte/tracy/)

<p align=center>
  <a href="https://github.com/contributte/tracy/actions"><img src="https://badgen.net/github/checks/contributte/tracy/master?cache=300"></a>
  <a href="https://codecov.io/gh/contributte/tracy"><img src="https://badgen.net/codecov/c/github/contributte/tracy"></a>
  <a href="https://packagist.org/packages/contributte/tracy"><img src="https://badgen.net/packagist/dm/contributte/tracy"></a>
  <a href="https://packagist.org/packages/contributte/tracy"><img src="https://badgen.net/packagist/v/contributte/tracy"></a>
</p>
<p align=center>
  <a href="https://packagist.org/packages/contributte/tracy"><img src="https://badgen.net/packagist/php/contributte/tracy"></a>
  <a href="https://github.com/contributte/tracy"><img src="https://badgen.net/github/license/contributte/tracy"></a>
  <a href="https://bit.ly/ctteg"><img src="https://badgen.net/badge/support/gitter/cyan"></a>
  <a href="https://bit.ly/cttfo"><img src="https://badgen.net/badge/support/forum/yellow"></a>
  <a href="https://contributte.org/partners.html"><img src="https://badgen.net/badge/sponsor/donations/F96854"></a>
</p>

<p align=center>
Website 🚀 <a href="https://contributte.org">contributte.org</a> | Contact 👨🏻‍💻 <a href="https://f3l1x.io">f3l1x.io</a> | Twitter 🐦 <a href="https://twitter.com/contributte">@contributte</a>
</p>

Contributte Tracy adds DI extensions for [Tracy](https://tracy.nette.org) in Nette Framework. When the DI container
fails to compile, the BlueScreen shows the container parameters and service definitions, and a multi logger lets
more loggers receive every message that Tracy logs.

## Usage

To install the latest version of `contributte/tracy`, use [Composer](https://getcomposer.org):

```bash
composer require contributte/tracy
```

Requires PHP 8.2 or later, Tracy 2.11.2 or later and `nette/di` 3.1 or later.

Register the extensions in your `config.neon`:

```neon
extensions:
	tracy.bluescreens: Contributte\Tracy\DI\TracyBlueScreensExtension
	tracy.logger: Contributte\Tracy\DI\LoggerExtension
```

The [documentation](.docs) describes what each extension adds.

## Versions

| State       | Version | Branch   | Nette  | PHP     |
|-------------|---------|----------|--------|---------|
| dev         | `^0.7`  | `master` | `3.1+` | `>=8.2` |
| stable      | `^0.6`  | `master` | `3.1+` | `>=8.1` |

## Development

Install the dependencies and run the checks:

```bash
make install   # install dependencies
make qa        # check code style and run static analysis
make tests     # run tests
```

Run `make` to list every target.

See [how to contribute](https://contributte.org/contributing.html) to this package.

This package is maintained by these authors.

<a href="https://github.com/f3l1x">
  <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/538058?v=3&s=80">
</a>

-----

Consider [supporting](https://contributte.org/partners.html) the **contributte** development team.
Thank you for using this package.
