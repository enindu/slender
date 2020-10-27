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

  /**
   * Filters constructor
   * 
   * @param Container $container
   */
  public function __construct(Container $container)
  {
    $this->container = $container;
  }

  /**
   * Get filters
   * 
   * @return array
   */
  public function getFilters(): array
  {
    return [
      new TwigFilter('asset', [$this, 'asset']),
      new TwigFilter('npm_asset', [$this, 'npmAsset']),
      new TwigFilter('page', [$this, 'page']),
      new TwigFilter('content', [$this, 'content'])
    ];
  }

  /**
   * Get asset URLs
   * 
   * @param string $file
   * 
   * @throws RuntimeError
   * @return string
   */
  public function asset(string $file): string
  {
    $filesystem = $this->container->get('filesystem');

    $fileExists = $filesystem->exists(__DIR__ . '/../../resources/assets' . $file);
    if(!$fileExists) {
      throw new RuntimeError('Cannot find ' . $file);
    }

    return $_ENV['app']['url'] . "/resources/assets" . $file;
  }

  /**
   * Get npm asset URLs
   * 
   * @param string $file
   * 
   * @throws RuntimeError
   * @return string
   */
  public function npmAsset(string $file): string
  {
    $filesystem = $this->container->get('filesystem');

    $fileExists = $filesystem->exists(__DIR__ . '/../../node_modules' . $file);
    if(!$fileExists) {
      throw new RuntimeError('Cannot find ' . $file);
    }

    return $_ENV['app']['url'] . "/node_modules" . $file;
  }

  /**
   * Get page URLs
   * 
   * @param string $path
   * 
   * @return string
   */
  public function page(string $path): string
  {
    return $_ENV['app']['url'] . $path;
  }

  /**
   * Get content
   * 
   * @param string $file
   * 
   * @throws RuntimeError
   * @return string
   */
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
