<?php

/**
 * @file
 * Contains \Drupal\tagadelic_taxonomy\Plugin\DefaultTagCloud.
 */

namespace Drupal\tagadelic_taxonomy\Plugin;

use Drupal\tagadelic_taxonomy\TagCloudInterface;

/**
 * Displays the default TagCloud.
 *
 * @TagCloudType(
 *   id = "default",
 *   label = @Translation("Default")
 * )
 */
class DefaultTagCloud implements TagCloudInterface {

  /**
   * {@inheritdoc}
   */
  function viewTagCloud() {
    return array(
      '#theme' => 'tagadelic_taxonomy_tagcloud',
    );
  }

}
