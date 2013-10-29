<?php

/**
 * @file
 * Contains \Drupal\tagadelic_taxonomy\Form\TagadelicTaxonomySettingsForm.
 */

namespace Drupal\tagadelic_taxonomy\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Cache\Cache;

/**
 * Configure tagadelic_taxonomy settings for this site.
 */
class TagadelicTaxonomySettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'tagadelic_taxonomy_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {
    $config = $this->config('tagadelic_taxonomy.settings');
    $form['enabled_vocabs'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Tagadelic taxonomy vocabularies'),
      '#options' => array(
        '' => t('All'),
      ),
      '#default_value' => $config->get('enabled_vocabs'),
      '#description' => t('Choose which vocabularies to create TagClouds for.'),
    );

    // @todo use a service from the taxonomy module to get the vocabs.
    $enabled_vocabs_manager = \Drupal::service('plugin.manager.tagadelic_taxonomy.enabled_vocabs');
    foreach($enabled_vocabs_manager->getDefinitions() as $id => $definition) {
      $form['enabled_vocabs']['#options'][$id] = $definition['label'];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    $config = $this->config('tagadelic_taxonomy.settings');
    $config->set('enabled_vocabs', $form_state['values']['enabled_vocabs']);
    $config->save();
    parent::submitForm($form, $form_state);

    // @todo Decouple from form: http://drupal.org/node/2040135.
    Cache::invalidateTags(array('config' => 'tagadelic_taxonomy.settings'));
  }

}

