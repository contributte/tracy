![](https://heatbadger.now.sh/github/readme/contributte/tracy/)

<p align=center>
  <a href="https://github.com/contributte/tracy/actions"><img src="https://badgen.net/github/checks/contributte/tracy/master?tracy=300"></a>
  <a href="https://codecov.io/gh/contributte/tracy"><img src="https://badgen.net/codecov/c/github/contributte/tracy?tracy=300"></a>
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

Tuned Tracy Bars, panels and BlueScreen integrations for easy development.

## Versions

| State  | Version | Branch   | Nette | PHP     |
|--------|---------|----------|-------|---------|
| dev    | `^0.7`  | `master` | 3.1+  | `>=8.2` |
| stable | `^0.6`  | `master` | 3.1+  | `>=8.1` |

## Installation

To install latest version of `contributte/tracy` use [Composer](https://getcomposer.org).

```bash
composer require contributte/tracy
```

## Usage

### TracyBlueScreens

`TracyBlueScreensExtension` adds a few BlueScreen panels for easier debugging.

```neon
extensions:
	tracy.bluescreens: Contributte\Tracy\DI\TracyBlueScreensExtension
```

![Container Builder - parameters][container-builder-parameters]
![Container Builder - definitions][container-builder-definitions]

### Logger

`LoggerExtension` replaces Tracy logger with `MultiLogger` so you can register additional logger services.

```neon
extensions:
	tracy.logger: Contributte\Tracy\DI\LoggerExtension
```

Extra loggers can then be wired in your app and added to the multi logger.

[container-builder-parameters]: .docs/assets/container-builder-parameters.png "Container Builder - parameters"
[container-builder-definitions]: .docs/assets/container-builder-definitions.png "Container Builder - definitions"

## Development

See [how to contribute](https://contributte.org) to this package. This package is currently maintained by these authors.

<a href="https://github.com/f3l1x">
    <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/538058?v=3&s=80">
</a>

-----

Consider to [support](https://contributte.org/partners) **contributte** development team.
Also thank you for using this package.
