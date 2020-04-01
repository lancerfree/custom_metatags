<?php

/**
 * @file
 * Contains \Drupal\custom_json\Form\CardBlockSettingsForm.
 */

namespace Drupal\custom_metatags\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form that configures specific metatags config.
 */
class MetatagsPageForm extends ConfigFormBase {

  /**
   * Current config name.
   *
   * @var string
   */
  protected $config_name;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'custom_metatags_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      $this->config_name
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $config_name = NULL) {
    $this->config_name = $config_name;
    $config = $this->config($config_name);
    $schema = \Drupal::service('config.typed')->getDefinition($config_name);

    $fields_map = [
      'label' => 'textfield',
      'text' => 'textarea',
    ];

    foreach ($schema['mapping'] AS $key_item => $value_item) {
      if (in_array($key_item, ['_core', 'langcode'])) {
        continue;
      }
      if (!empty($value_item['type']) && !empty($fields_map[$value_item['type']])) {
        $form[$key_item] = array(
          '#type' => $fields_map[$value_item['type']],
          '#title' => $this->t($value_item['label']),
          '#default_value' => $config->get($key_item),
          '#required' => FALSE,
        );
      }
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $config = $this->config($this->config_name);
    $schema = \Drupal::service('config.typed')->getDefinition($this->config_name);

    foreach ($values AS $key_item => $value_item) {
      if ($schema['mapping'][$key_item]) {
        $config->set($key_item, $value_item);
      }
    }

    $config->save();
  }
}