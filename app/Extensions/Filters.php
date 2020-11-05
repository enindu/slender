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
   * Get filters function
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
   * Asset function
   * 
   * @param string $file
   * 
   * @throws RuntimeError
   * @return string
   */
  public function asset(string $file): string
  {
    // Get filesystem library
    $filesystem = $this->container->get('filesystem');

    // Check file exists
    $fileExists = $filesystem->exists(__DIR__ . '/../../resources/assets' . $file);
    if(!$fileExists) {
      throw new RuntimeError('Cannot find ' . $file);
    }

    // Return asset URL
    return $_ENV['app']['url'] . "/resources/assets" . $file;
  }

  /**
   * npm asset function
   * 
   * @param string $file
   * 
   * @throws RuntimeError
   * @return string
   */
  public function npmAsset(string $file): string
  {
    // Get filesystem library
    $filesystem = $this->container->get('filesystem');

    // Check file exists
    $fileExists = $filesystem->exists(__DIR__ . '/../../node_modules' . $file);
    if(!$fileExists) {
      throw new RuntimeError('Cannot find ' . $file);
    }

    // Return npm asset URL
    return $_ENV['app']['url'] . "/node_modules" . $file;
  }

  /**
   * Page function
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
   * Content function
   * 
   * @param string $file
   * 
   * @throws RuntimeError
   * @return string
   */
  public function content(string $file): string
  {
    // Get filesystem library
    $filesystem = $this->container->get('filesystem');

    // Check file exists
    $fileExists = $filesystem->exists(__DIR__ . '/../../resources/assets' . $file);
    if(!$fileExists) {
      throw new RuntimeError('Cannot find ' . $file);
    }

    // Check file content
    $fileContent = file_get_contents(__DIR__ . '/../../resources/assets' . $file);
    if(!$fileContent) {
      throw new RuntimeError('Cannot get content from ' . $file);
    }

    // Return file content
    return $fileContent;
  }
}
