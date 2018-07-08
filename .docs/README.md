# Tracy(debugging)

## Content

- [TracyBlueScreensExtension - better BlueScreen panels](#tracybluescreensextension)
- [NavigationPanelExtension - navigate easily through all presenters](#navigationpanelextension)

## TracyBlueScreensExtension

`TracyBlueScreensExtension` adds a few BlueScreen panels for better debugging.

```yaml
extensions:
    tracy.bluescreens: Contributte\Tracy\DI\TracyBlueScreensExtension
```

![Container Builder - parameters][container-builder-parameters]
![Container Builder - definitions][container-builder-definitions]

[container-builder-parameters]: https://raw.githubusercontent.com/contributte/tracy/master/.docs/assets/container-builder-parameters.png "Container Builder - parameters"
[container-builder-definitions]: https://raw.githubusercontent.com/contributte/tracy/master/.docs/assets/container-builder-definitions.png "Container Builder - definitions"

## NavigationPanelExtension

`NavigationPanelExtension` adds a Tracy bar panel for navigation across all presenters.

```yaml
extensions:
    tracy.navigation: Contributte\Tracy\DI\NavigationTracyPanel
```

Links are generated for presenters which:

- Are registered into DIC (should be all of them, if application is not misconfigured)
- Inherits from Nette\Application\UI\Presenter
- Have at least one action or render method without parameters (or with optional parameters only)
