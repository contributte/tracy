# Tracy Design

Contributte Tracy adds two tabs to the Tracy BlueScreen that a developer sees when the Nette DI container fails to
compile. It also ships static 500 error pages in `src/resources/`, which no class uses.

## Principles

- **Tracy draws, we fill.** Panels return `['tab' => ..., 'panel' => ...]` HTML and Tracy renders it with its own
  CSS and JavaScript. The package ships no stylesheet or script.
- **Show raw data, not a summary.** Panels dump `ContainerBuilder` data with `Tracy\Dumper::toHtml()`, fully expanded,
  so the developer can search the page.
- **Stay silent when not relevant.** A panel returns `null` for any exception that did not come from
  `Nette\DI\Compiler::compile`, so it never adds an empty tab.

## Inventory

- Tab "ContainerBuilder - parameters": `src/BlueScreen/ContainerBuilderParametersBlueScreen.php`
- Tab "ContainerBuilder - definitions": `src/BlueScreen/ContainerBuilderDefinitionsBlueScreen.php`, with an optional
  "Definition for '{service}'" section above "All definitions"
- Registration: `src/DI/TracyBlueScreensExtension.php`
- Static error pages, unused: `src/resources/500.html`, `500.json`, `500.txt`

## Layout

- Each tab is one Tracy BlueScreen section. The definitions tab wraps each part in a `div` with an `h3` heading.
- Width, spacing and the section toggle come from Tracy.

## Typography

- Inherits Tracy's BlueScreen fonts. The panels set no styles.

## Colors and Tokens

- No colors of our own; dump colors come from Tracy's dumper CSS.
- `src/resources/500.html` has inline `<style>` blocks (`#error-body`, `#tracy-error`); they are the only CSS in the
  repository.

## States

- Empty: no tab is shown when the exception does not match.
- Loading: none; dumps use `Dumper::LIVE`, so Tracy's JavaScript builds them when the page loads.
- Error: the panels are the error state; they don't catch their own failures.

## Accessibility

- Headings are real `h3` elements; the rest follows Tracy's BlueScreen.
- Known gaps: `Dumper::LIVE` dumps need JavaScript to show anything. "All definitions" is fully expanded
  (`Dumper::COLLAPSE => false`), so a large application produces a very long page.

## Dark Mode

- Follows whatever Tracy's BlueScreen supports; the panels add nothing. The pages in `src/resources/` are light only.

## Responsive

- Follows Tracy. `500.html` uses a fixed `width: 500px` box and has no breakpoints.

## Screenshots

- `.docs/assets/container-builder-parameters.png` and `container-builder-definitions.png` show the two tabs and are
  embedded in `.docs/README.md`. Take them by hand from an app with a broken service definition.
- `.docs/assets/navigation-panel.png` shows a navigation panel that is no longer in `src/`; nothing links to it.

## Changing the UI

- The tab titles are what users search for in the BlueScreen; keep "ContainerBuilder - parameters" and
  "ContainerBuilder - definitions".
- `500.html` contains two documents: a static page and, after it, a copy of Tracy's PHP error template with a
  `<?php` block. Served as plain HTML, the PHP code would show. Split or remove it before wiring it anywhere.

## Checklist

- [ ] A panel still returns `null` for exceptions outside `Nette\DI\Compiler::compile`
- [ ] Tab titles are unchanged
- [ ] `make tests` passes, including the panel count in `TracyBlueScreensExtension.phpt`
- [ ] Screenshots in `.docs/assets/` retaken if a tab looks different
