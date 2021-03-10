# Contributte Tracy (debugging)

Tuned Tracy Bars/Panels/BlueScreens for easy-developing.

## Content

- [Setup](#setup)
- [TracyBlueScreens - better BlueScreen panels](#tracybluescreen)
- [NavigationPanel - navigate easily through all presenters](#navigationpanel)

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

[container-builder-parameters]: https://raw.githubusercontent.com/contributte/tracy/master/.docs/assets/container-builder-parameters.png "Container Builder - parameters"
[container-builder-definitions]: https://raw.githubusercontent.com/contributte/tracy/master/.docs/assets/container-builder-definitions.png "Container Builder - definitions"

## NavigationPanel

`NavigationPanelExtension` adds a Tracy bar panel for navigating across all presenters.

```neon
extensions:
	tracy.navigation: Contributte\Tracy\DI\NavigationPanelExtension

tracy.navigation:
	debug: %debugMode%
```

Links are generated for presenters which:

- Are registered into DIC (should be all of them, if the application is not misconfigured).
- Inherit from `Nette\Application\UI\Presenter`.
- Have at least one action or render method without parameters (or with optional parameters only).

![Navigation panel](assets/navigation-panel.png?raw=true)
