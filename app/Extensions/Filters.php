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
      new TwigFilter('page', [$this, 'page']),
      new TwigFilter('asset', [$this, 'asset']),
      new TwigFilter('npm_asset', [$this, 'npmAsset']),
      new TwigFilter('file', [$this, 'file']),
      new TwigFilter('content', [$this, 'content']),
      new TwigFilter('limit', [$this, 'limit']),
      new TwigFilter('human_date', [$this, 'humanDate']),
      new TwigFilter('markdown', [$this, 'markdown'])
    ];
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
   * Asset function
   * 
   * @param string $file
   * @param string $type
   * 
   * @throws RuntimeError
   * @return string
   */
  public function asset(string $file, string $type): string
  {
    // Get filesystem library
    $filesystem = $this->container->get('filesystem');

    // Check file exists
    $fileExists = $filesystem->exists(__DIR__ . '/../../resources/' . $type . '/assets' . $file);
    if(!$fileExists) {
      throw new RuntimeError('Cannot find ' . $file);
    }

    // Return asset URL
    return $_ENV['app']['url'] . "/resources/" . $type . "/assets" . $file;
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
   * File function
   * 
   * @param string $file
   * @param string $type
   * 
   * @throws RuntimeError
   * @return string
   */
  public function file(string $file, string $type): string
  {
    // Get filesystem library
    $filesystem = $this->container->get('filesystem');

    // Check file exists
    $fileExists = $filesystem->exists(__DIR__ . '/../../uploads/' . $type . '/' . $file);
    if(!$fileExists) {
      throw new RuntimeError('Cannot find ' . $file);
    }

    // Return file URL
    return $_ENV['app']['url'] . "/uploads/" . $type . "/" . $file;
  }

  /**
   * Content function
   * 
   * @param string $file
   * @param string $type
   * 
   * @throws RuntimeError
   * @return string
   */
  public function content(string $file, string $type): string
  {
    // Get filesystem library
    $filesystem = $this->container->get('filesystem');

    // Check file exists
    $fileExists = $filesystem->exists(__DIR__ . '/../../uploads/' . $type . '/' . $file);
    if(!$fileExists) {
      throw new RuntimeError('Cannot find ' . $file);
    }

    // Check file content
    $fileContent = file_get_contents(__DIR__ . '/../../uploads/' . $type . '/' . $file);
    if(!$fileContent) {
      throw new RuntimeError('Cannot get content from ' . $file);
    }

    // Return file content
    return $fileContent;
  }

  /**
   * Limit function
   * 
   * @param string $text
   * @param int    $limit
   * 
   * @return string
   */
  public function limit(string $text, int $length = 100): string
  {
    // Check text length
    $textLength = strlen($text);
    if($textLength < $length) {
      return $text;
    }

    // Return shorten text
    return substr($text, 0, $length - 3) . "...";
  }

  /**
   * Human date function
   * 
   * @param string $date
   * 
   * @return string
   */
  public function humanDate(string $date): string
  {
    // Get clock library
    $clock = $this->container->get('clock');

    // Return human date
    return $clock::parse($date)->diffForHumans($clock::now());
  }

  /**
   * Markdown function
   * 
   * @param string $content
   * 
   * @return string
   */
  public function markdown(string $content): string
  {
    // Get markdown library
    $markdown = $this->container->get('markdown');

    // Return HTML content
    return $markdown->text($content);
  }
}
