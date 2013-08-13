<?php

/**
 * @file
 * Contains TagadelicCloud.
 */

class TagadelicCloud {

  /**
   * An identifier for this cloud. Must be unique.
   *
   * @var int|string
   */
  protected $id = "";

  /**
   * List of the tags in this cloud.
   *
   * @var array
   */
  protected $tags = array();

  /**
   * Amount of steps to weight the cloud in. Defaults to 6.
   *
   * @var int
   */
  protected $steps = 6;

  /**
   * Flag to indicate whether to recalculateTagWeights the tag weights.
   *
   * @var bool
   */
  protected $needsRecalc = TRUE;

  /**
   * An instance of TagadelicDrupalWrapper. Used primarily for testing purposes.
   *
   * @var TagadelicDrupalWrapper
   */
  protected $drupalWrapper;

  /**
   * Initalize the cloud.
   *
   * @param int $id
   *   Integer, identifies this cloud; used for caching and re-fetching of
   *   previously built clouds.
   *
   * @param array $tags
   *   Provide tags on building. Tags can be added later, using $this->addTag().
   *
   * @return TagadelicCloud.
   */
  public function __construct($id, $tags = array()) {
    $this->id = $id;
    $this->tags = $tags;
  }

  /**
   * Getter for id
   * @ingroup getters
   * @returns Integer id of this cloud
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Getter for tags
   * @ingroup getters
   * @returns Array list of tags
   */
  public function getTags() {
    $this->recalculateTagWeights();
    return $this->tags;
  }

  /**
   * Add a new tag to the cloud
   * @param $tag TagadelicTag
   *   instance of TagadelicTag.
   *
   * return $this, for chaining.
   */
  public function addTag($tag) {
    $this->tags[] = $tag;
    return $this;
  }

  /**
   * Sets $this->drupalWrapper to an instance of TagadelicDrupalWrapper.
   *
   * @param TagadelicDrupalWrapper $wrapper
   *   A mock Drupal instance to use for testing.
   *
   * @return TagadelicCloud
   *   The current instance of TagadelicCloud.
   */
  public function setDrupalWrapper($wrapper) {
    $this->drupalWrapper = $wrapper;
    return $this;
  }

  /**
   * Get an instance of TagadelicDrupalWrapper.
   *
   * @return TagadelicDrupalWrapper
   *   Value in $this->drupal.
   */
  public function getDrupalWrapper() {
    if (empty($this->drupalWrapper)) {
      $this->setDrupalWrapper(new TagadelicDrupalWrapper());
    }
    return $this->drupalWrapper;
  }

  /**
   * Instantiate an instance of TagadelicCloud from the cache.
   *
   * @param int $id
   *   The id of the TagadelicCloud instance to retrieve from the cache.
   * @param stdObject $drupal
   *   The current Drupal instance.
   *
   * @return TagadelicCloud
   *   A new instance from the cache.
   */
  public static function fromCache($id, $drupal) {
    $cache_id = "tagadelic_cloud_{$id}";
    return $drupal->cache_get($cache_id);
  }

  /**
   * Writes the cloud to cache. Will recalculateTagWeights if needed.
   *
   * @return TagadelicCloud
   *   The current instance.
   */
  public function toCache() {
    $cache_id = "tagadelic_cloud_{$this->id}";
    $this->drupal()->cache_set($cache_id, $this);
    return $this;
  }

  /**
   * Sorts the tags by a given property.
   *
   * @param string $property
   *   The property to sort the tags on.
   *
   * @return TagadelicCloud
   *   The current instance of TagadelicCloud.
   */
  public function sortTagsBy($property) {
    if ($property == "random") {
      $this->getDrupalWrapper()->shuffle($this->tags);
    }
    else {
      // PHP Bug: https://bugs.php.net/bug.php?id=50688 - Supress the error.
      @usort($this->tags, array($this, "sortBy{$property}"));
    }
    return $this;
  }

  /**
   * Recalculates the weights of tags.
   *
   * @return TagadelicCloud
   *   The current instance of TagadelicCloud, for chaining?
   */
  protected function recalculateTagWeights() {
    $tags = array();
    // Find minimum and maximum log-count.
    $min = 1e9;
    $max = -1e9;
    foreach ($this->tags as $id => $tag) {
      $min = min($min, $tag->distributed());
      $max = max($max, $tag->distributed());
      $tags[$id] = $tag;
    }
    // Note: we need to ensure the range is slightly too large to make sure even
    // the largest element is rounded down.
    $range = max(.01, $max - $min) * 1.0001;
    foreach ($tags as $id => $tag) {
      $this->tags[$id]->set_weight(1 + floor($this->steps * ($tag->distributed() - $min) / $range));
    }
    return $this;
  }

  /**
   * Sort by name.
   *
   * @param string $a
   *   A string.
   * @param string $b
   *   Another string.
   *
   * @return int
   *   <0 if $a is less than $b, >0 if $b is less than $a, 1 if they are equal.
   */
  protected function sortByName($a, $b) {
    return strcoll($a->get_name(), $b->get_name());
  }

  /**
   * Sort by count.
   *
   * @param int $a
   *   An integer.
   * @param int $b
   *   Another integer.
   *
   * @return int
   *   <0 if $a is less than $b, >0 if $b is less than $a, 1 if they are equal.
   */
  protected function sortByCount($a, $b) {
    $ac = $a->get_count();
    $bc = $b->get_count();
    if ($ac == $bc) {
      return 0;
    }
    // Highest first, High to low.
    return ($ac < $bc) ? 1 : -1;
  }
}
