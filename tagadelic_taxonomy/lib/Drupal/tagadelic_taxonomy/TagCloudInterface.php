<?php

/**
 * @file
 * Contains \Drupal\tagadelic_taxonomy\TagCloudInterface.
 */

namespace Drupal\tagadelic_taxonomy;

/**
 * Defines the interface for TagClouds.
 */
interface TagCloudInterface {

  /**
   * Returns a render array to display a TagCloud for a specific vocabulary.
   *
   * @return array
   *   A render array.
   */
  public function viewTagCloud();

}
