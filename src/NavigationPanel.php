<?php declare(strict_types = 1);

namespace Contributte\Tracy;

use Nette\Application\LinkGenerator;
use Nette\Application\PresenterFactory;
use Nette\Application\UI\InvalidLinkException;
use Nette\Utils\Strings;
use ReflectionClass;
use ReflectionMethod;
use Tracy\IBarPanel;

class NavigationPanel implements IBarPanel
{

	/** @var PresenterFactory */
	private $presenterFactory;

	/** @var LinkGenerator */
	private $linkGenerator;

	/** @var string[]&class-string<object>[] */
	private $presenters;

	/** @var mixed[] */
	private $tree = [];

	/**
	 * @param string[]&class-string<object>[] $presenters
	 */
	public function __construct(
		PresenterFactory $presenterFactory,
		LinkGenerator $linkGenerator,
		array $presenters
	)
	{
		$this->presenterFactory = $presenterFactory;
		$this->linkGenerator = $linkGenerator;
		$this->presenters = $presenters;

		$this->tree = $this->buildTree();
	}

	public function getTab(): string
	{
		ob_start();
		require __DIR__ . '/templates/NavigationPanel/tab.phtml';
		return (string) ob_get_clean();
	}

	public function getPanel(): string
	{
		// phpcs:ignore
		$tree = $this->tree;
		ob_start();
		require __DIR__ . '/templates/NavigationPanel/panel.phtml';
		return (string) ob_get_clean();
	}

	/**
	 * @return mixed[]
	 */
	private function buildTree(): array
	{
		$tree = [];

		foreach ($this->presenters as $presenter) {
			$rc = new ReflectionClass($presenter);
			$methods = $rc->getMethods(ReflectionMethod::IS_PUBLIC);
			$filteredMethods = [];

			$presenter = $this->presenterFactory->unformatPresenterClass($presenter);

			if ($presenter === null) {
				continue;
			}

			$parts = explode(':', $presenter);
			$presenter = array_pop($parts);
			$module = implode(':', $parts);

			foreach ($methods as $method) {
				if (Strings::startsWith($method->name, 'action') || Strings::startsWith($method->name, 'render')) {
					$filteredMethods[] = $method;
				}
			}

			// Try create links to all available presenters
			foreach ($filteredMethods as $method) {

				$action = str_replace(['action', 'render'], '', $method->getName());
				$action = lcfirst($action);

				if (isset($tree[$module][$presenter][$action])) {
					continue;
				}

				try {
					$link = $this->linkGenerator->link($module !== '' ? sprintf('%s:%s:%s', $module, $presenter, $action) : sprintf('%s:%s', $presenter, $action));
					$tree[$module][$presenter][$action] = $link;
				} catch (InvalidLinkException $e) {
					// Just trying generate link
				}
			}

			if (!isset($tree[$module][$presenter]['default'])) {
				try {
					$link = $this->linkGenerator->link($module !== '' ? sprintf('%s:%s:', $module, $presenter) : sprintf('%s:', $presenter));
					$tree[$module][$presenter]['default'] = $link;
				} catch (InvalidLinkException $e) {
					// Just trying generate link
				}
			}
		}

		return $tree;
	}

}
