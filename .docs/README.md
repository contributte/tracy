# Contributte Tracy (debugging)

Tuned Tracy Bars/Panels/BlueScreens for easy-developing.

## Content

- [Setup](#setup)
- [TracyBlueScreens - better BlueScreen panels](#tracybluescreen)
- [Logger - register additional Tracy loggers](#logger)
- [Sanitized Markdown - safe AI-friendly exception export](#sanitized-markdown)

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

## Sanitized Markdown

`SanitizedMarkdownExtension` registers a BlueScreen panel that renders a sanitized Markdown document for the current exception. Values are replaced by type descriptors like `string(40)` or `array(3)`, so the output can be shared with an AI agent without leaking request or payload data.

```neon
extensions:
	tracy.sanitizedMarkdown: Contributte\Tracy\DI\SanitizedMarkdownExtension
```

You can also inject `Contributte\Tracy\SanitizedMarkdownRenderer` and render the Markdown string directly:

```php
$markdown = $sanitizedMarkdownRenderer->render($throwable);
```

[container-builder-parameters]: https://raw.githubusercontent.com/contributte/tracy/master/.docs/assets/container-builder-parameters.png "Container Builder - parameters"
[container-builder-definitions]: https://raw.githubusercontent.com/contributte/tracy/master/.docs/assets/container-builder-definitions.png "Container Builder - definitions"
