<?php declare(strict_types = 1);

use Contributte\Tester\Toolkit;
use Contributte\Tester\Utils\ContainerBuilder;
use Contributte\Tracy\DI\SanitizedMarkdownExtension;
use Contributte\Tracy\SanitizedMarkdownRenderer;
use Nette\DI\Compiler;
use Tester\Assert;
use Tracy\Debugger;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(static function (): void {
	$rf = new ReflectionClass(Debugger::getBlueScreen());
	$panelsrf = $rf->getProperty('panels');

	Assert::count(0, $panelsrf->getValue(Debugger::getBlueScreen()));

	$container = ContainerBuilder::of()
		->withCompiler(static function (Compiler $compiler): void {
			$compiler->addExtension('sanitizedMarkdown', new SanitizedMarkdownExtension());
		})
		->build();

	Assert::count(0, $panelsrf->getValue(Debugger::getBlueScreen()));

	$container->initialize();

	Assert::count(1, $panelsrf->getValue(Debugger::getBlueScreen()));
	Assert::type(SanitizedMarkdownRenderer::class, $container->getByType(SanitizedMarkdownRenderer::class));

	$panel = $panelsrf->getValue(Debugger::getBlueScreen())[0];
	$output = $panel(new RuntimeException('super-secret-message'));

	Assert::same('Sanitized Markdown', $output['tab']);
	Assert::contains('Message: string(20)', $output['panel']);
	Assert::notContains('super-secret-message', $output['panel']);
});
