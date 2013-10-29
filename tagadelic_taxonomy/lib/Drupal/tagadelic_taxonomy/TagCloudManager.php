<?php

/**
 * @file
 * Contains \Drupal\tagadelic_taxonomy\TagCloudManager.
 */

namespace Drupal\tagadelic_taxonomy;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageManager;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Manages tagadelic_taxonomy type plugins.
 */
class TagCloudManager extends DefaultPluginManager {

  /**
   * {@inheritdoc}
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, LanguageManager $language_manager, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/DefaultTagCloud', $namespaces, 'Drupal\tagadelic_taxonomy\Annotation\TagCloudType');

    $this->alterInfo($module_handler, 'tagadelic_taxonomy_tagcloud_type_info');
    $this->setCacheBackend($cache_backend, $language_manager, 'enabled_vocabs');
  }

}
