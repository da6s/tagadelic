<?php

/**
 * @file
 * Contains \Drupal\tagadelic_taxonomy\Annotation\TagCloudType.
 */

namespace Drupal\tagadelic_taxonomy\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a TagCloud type annotation object.
 *
 * @Annotation
 */
class TagCloudType extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the tag cloud type.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $label;

}
