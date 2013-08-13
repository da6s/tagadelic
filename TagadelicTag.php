<?php

/**
 * @file
 * Contains TagadelicTag.
 */

class TagadelicTag {

  /**
   * Identifier of this tag.
   *
   * @var int
   */
  protected $id = 0;

  /**
   * A human readable name for this tag.
   *
   * @var string
   */
  protected $name = "";

  /**
   * A human readable piece of HTML-formatted text.
   *
   * @var string
   */
  protected $description = "";

  /**
   * Where this tag will point to. If left empty, tag will not be linked. Can be a full url too.
   *
   * @var string
   */
  protected $link = "";

  /**
   * Absolute count for the weight. Weight, i.e. tag-size will be extracted from this.
   *
   * @var float
   */
  protected $count = 0.0000001;

  /**
   * Flag to toggle XSS filtering of text fields.
   *
   * @var bool
   */
  protected $isDirty = TRUE;

  /**
   * @todo What purpose does this property do?
   *
   * @var float
   */
  protected $weight = 0.0;

  /**
   * Contains the DrupalWrapper, mostly for testablity.
   *
   * @var null
   */
  protected $drupalWrapper = NULL;

  /**
   * Initalizes a new TagadelicTag.
   *
   * @param int $id
   *   The identifier of this tag.
   * @param string $name
   *   A human readable name describing this tag.
   * @param int $count
   *   The count of the tag, used to calculate the weight.
   */
  protected function __construct($id, $name, $count) {
    $this->id    = $id;
    $this->name  = $name;
    if ($count != 0) {
      $this->count = $count;
    }
  }

  /**
   * Renders the Tag as an HTML link to its source.
   */
  protected function render() {
    $this->clean();
    $attributes = $options = array();

    $attributes["title"] = !empty($this->description) ? $this->description : '';
    $attributes["class"][] = $this->weight > 0 ? "level{$this->weight}" : '';
    $options["attributes"] = !empty($attributes) ? $attributes : array();

    return $this->getDrupalWrapper()->l($this->name, $this->link, $options);
  }

  /**
   * Gets the ID of the current tag.
   *
   * @return int
   *   The identifier for this tag.
   */
  protected function getId() {
    return $this->id;
  }

  /**
   * Gets the name property.
   *
   * @return string
   *   The human readable name.
   */
  protected function getName() {
    $this->clean();
    return $this->name;
  }

  /**
   * Gets the description property.
   *
   * @return string
   *   The human readable description.
   */
  protected function getDescription() {
    $this->clean();
    return $this->description;
  }

  /**
   * Gets the weight of current tag.
   *
   * @return float
   *   The weight of this tag.
   */
  protected function getWeight() {
    return $this->weight;
  }

  /**
   * Gets the count of the current tag.
   *
   * @return int
   *   The count as provided when initializing the object.
   */
  protected function getCount() {
    return $this->count;
  }

  /**
   * Sets the optional description.
   * A tag may have a description
   * @param $description String a description
   */
  protected function setDescription($description) {
    $this->description = $description;
  }

  /**
   * Sets the HTML for a link to a resource.
   *
   * @param string $link
   *   A link to a resource that represents the tag. e.g. a listing with all
   *   things tagged with Tag, or the article that represents the tag.
   */
  protected function setLink($link) {
    $this->link = $link;
  }

  /**
   * Sets the weight for a tag.
   *
   * @param int $weight
   *   The weight of the tag.
   *
   * @return TagadelicTag
   *   The current instance of TagadelicTag.
   */
  protected function setWeight($weight) {
    $this->weight = $weight;
    return $this;
  }

  /**
   * Sets $this->drupalWrapper to an instance of TagadelicDrupalWrapper.
   *
   * @param TagadelicDrupalWrapper $wrapper
   *   An instance of TagadelicDrupalWrapper.
   *
   * @return TagadelicTag
   *   The current instance of TagadelicTag.
   */
  protected function setDrupalWrapper($wrapper) {
    $this->drupalWrapper = $wrapper;
    return $this;
  }

  /**
   * Gets an instance of TagadelicDrupalWrapper, creating a new one if needed.
   *
   * @return TagadelicDrupalWrapper
   *   An instance of TagadelicDrupalWrapper.
   */
  protected function getDrupalWrapper() {
    if (empty($this->drupalWrapper)) {
      $this->setDrupalWrapper(new TagaDelicDrupalWrapper());
    }
    return $this->drupalWrapper;
  }

  /**
   * Flags the tag to be filtered for XSS through TagadelicTag::clean().
   *
   * @param bool $value
   *   If TRUE, $name and $description will be filtered for XSS.
   */
  protected function setDirty($value) {
    $this->isDirty = $value;
  }

  /**
   * Calculates a more evenly distributed value.
   */
  protected function distributed() {
    return log($this->count);
  }

  /**
   * Filters strings for XSS.
   */
  protected function clean() {
    if ($this->isDirty) {
      $this->name = $this->getDrupalWrapper()->check_plain($this->name);
      $this->description = $this->getDrupalWrapper()->check_plain($this->description);
      $this->forceClean();
    }
  }

}
