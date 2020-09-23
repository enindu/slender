<?php

namespace App\Extensions;

use DI\Container;
use Twig\Error\RuntimeError;
use Twig\Extension\AbstractExtension;
use Twig\Extension\ExtensionInterface;
use Twig\TwigFilter;

class Filters extends AbstractExtension implements ExtensionInterface
{
  private $container;

  public function __construct(Container $container)
  {
    $this->container = $container;
  }

  public function getFilters(): array
  {
    return [
      new TwigFilter('asset', [$this, 'asset']),
      new TwigFilter('node_asset', [$this, 'nodeAsset']),
      new TwigFilter('page', [$this, 'page']),
      new TwigFilter('content', [$this, 'content'])
    ];
  }

  public function asset(string $file): string
  {
    $filesystem = $this->container->get('filesystem');

    $fileExists = $filesystem->exists(__DIR__ . '/../../resources/assets' . $file);

    if(!$fileExists) {
      throw new RuntimeError('Cannot find ' . $file);
    }

    return $_ENV['APP_URL'] . "/resources/assets" . $file;
  }

  public function nodeAsset(string $file): string
  {
    $filesystem = $this->container->get('filesystem');

    $fileExists = $filesystem->exists(__DIR__ . '/../../node_modules' . $file);

    if(!$fileExists) {
      throw new RuntimeError('Cannot find ' . $file);
    }

    return $_ENV['APP_URL'] . "/node_modules" . $file;
  }

  public function page(string $path): string
  {
    return $_ENV['APP_URL'] . $path;
  }

  public function content(string $file): string
  {
    $filesystem = $this->container->get('filesystem');

    $fileExists = $filesystem->exists(__DIR__ . '/../../resources/assets' . $file);

    if(!$fileExists) {
      throw new RuntimeError('Cannot find ' . $file);
    }

    $fileContent = file_get_contents(__DIR__ . '/../../resources/assets' . $file);

    if(!$fileContent) {
      throw new RuntimeError('Cannot get content from ' . $file);
    }

    return $fileContent;
  }
}
