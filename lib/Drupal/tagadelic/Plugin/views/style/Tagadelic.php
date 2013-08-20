<?php

/**
 * @file
 * Contains \Drupal\tagadelic\Plugin\views\style\Tagadelic.
 */

namespace Drupal\tagadelic\Plugin\views\style;

use Drupal\views\Plugin\views\style\StylePluginBase;
use Drupal\Component\Annotation\Plugin;
use Drupal\Core\Annotation\Translation;
use Drupal\tagadelic\Tag;
use Drupal\tagadelic\TagadelicTagCloud;

/**
 * Provides a Tagadelic views style plugin.
 *
 * @Plugin(
 *   id = "tagadelic",
 *   title = @Translation("Tagadelic"),
 *   help = @Translation("Generate a tag cloud based on a views results."),
 *   theme = "views_view_tagadelic",
 *   module = "tagadelic",
 *   display_types = {"normal"}
 * )
 */
class Tagadelic extends StylePluginBase {

  /**
   * Does the style plugin allow to use style plugins.
   *
   * @var bool
   */
  protected $usesRowPlugin = TRUE;

  /**
   * Does the style plugin support custom css class for the rows.
   *
   * @var bool
   */
  protected $usesRowClass = TRUE;

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['levels'] = array('default' => 6);
    $options['css_class_prefix'] = array('default' => 'level');
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, &$form_state) {
    parent::buildOptionsForm($form, $form_state);
    $form['levels'] = array(
      '#type' => 'textfield',
      '#title' => t('Number of levels'),
      '#default_value' => $this->options['levels'],
      '#description' => t('This number determines the number of different sized objects that will be displayed.'),
      '#maxlength' => 36,
    );
    $form['css_prefix'] = array(
      '#type' => 'textfield',
      '#title' => t('CSS prefix'),
      '#default_value' => $this->options['css_prefix'],
      '#description' => t('By default, Tagadelic will prefix class names with "level" (eg. "level4") -- you may specify another prefix here.'),
      '#maxlength' => 60,
    );
  }

  /**
   * Return the token-replaced row or column classes for the specified result.
   *
   * @param int $result_index
   *   The delta of the result item to get custom classes for.
   * @param int $level
   *   The level of the current result item.
   *
   * @return string
   *   A space-delimited string of classes.
   */
  public function getCustomClass($result_index, $level) {
    $class = $this->options['css_prefix'] . $level;
    if ($this->usesFields() && $this->view->field) {
      $class = strip_tags($this->tokenizeValue($class, $result_index));
    }

    $classes = explode(' ', $class);
    foreach ($classes as &$class) {
      $class = drupal_clean_css_identifier($class);
    }
    return implode(' ', $classes);
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    if (empty($this->view->rowPlugin)) {
      debug('Drupal\tagadelic\Plugin\views\style\Tagadelic: Missing row plugin');
      return;
    }

    $tags = array();

    // Iterate over each rendered views result row.
    foreach ($this->view->result as $row) {
      $new_tag = new Tag($row->taxonomy_term_data_node_tid, $row->taxonomy_term_data_node_name, $row->taxonomy_term_data_node_tid_1);
      $new_tag->setLink('taxonomy/term/' . $row->taxonomy_term_data_node_tid);
      $tags[] = $new_tag;
    }

    $cloud = new TagadelicTagCloud("Tagadelic", $tags);

    $rows = array();
    foreach ($cloud->getTags() as $tag) {
      $rows[] = $tag->render();
    }

    $output = theme($this->themeFunctions(),
      array(
        'view' => $this->view,
        'options' => $this->options,
        'rows' => $rows
      ));

    return $output;
  }

}
