# Contributte Tracy (debugging)

Tuned Tracy Bars/Panels/BlueScreens for easy-developing.

## Content

- [Setup](#setup)
- [TracyBlueScreens - better BlueScreen panels](#tracybluescreen)
- [Logger - register additional Tracy loggers](#logger)

## Setup

```bash
composer require contributte/tracy
```

## TracyBlueScreens

`TracyBlueScreensExtension` adds a few BlueScreen panels for easier debugging.

```neon
extensions:
	tracy.bluescreens: Contributte\Tracy\DI\TracyBlueScreensExtension
```

![Container Builder - parameters][container-builder-parameters]
![Container Builder - definitions][container-builder-definitions]

## Logger

`LoggerExtension` replaces Tracy logger with `MultiLogger` so you can register additional logger services.

```neon
extensions:
	tracy.logger: Contributte\Tracy\DI\LoggerExtension
```

Extra loggers can then be wired in your app and added to the multi logger.

[container-builder-parameters]: https://raw.githubusercontent.com/contributte/tracy/master/.docs/assets/container-builder-parameters.png "Container Builder - parameters"
[container-builder-definitions]: https://raw.githubusercontent.com/contributte/tracy/master/.docs/assets/container-builder-definitions.png "Container Builder - definitions"
