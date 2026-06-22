<?php

namespace Drupal\dagda_dither\Plugin\ImageEffect;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Image\ImageInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\image\Attribute\ImageEffect;
use Drupal\image\ConfigurableImageEffectBase;

/**
 * Provide a Dither ImageEffect to use in Image Style.
 */
#[ImageEffect(
  id: "dither",
  label: new TranslatableMarkup("Dither Effect"),
  description: new TranslatableMarkup("Apply dithering and color reduction effect with specific levels and modulation.")
)]
class DitherImageEffect extends ConfigurableImageEffectBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'level_low' => 0,
      'level_high' => 95,
      'colorize_amount' => 10,
      'modulate_brightness' => 115,
      'modulate_saturation' => 90,
      'colors' => 64,
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['level_low'] = [
      '#type' => 'number',
      '#title' => $this->t('Level Low (%)'),
      '#description' => $this->t('Lower threshold for level adjustment (0-100)'),
      '#default_value' => $this->configuration['level_low'],
      '#min' => 0,
      '#max' => 100,
      '#required' => TRUE,
    ];

    $form['level_high'] = [
      '#type' => 'number',
      '#title' => $this->t('Level High (%)'),
      '#description' => $this->t('Upper threshold for level adjustment (0-100)'),
      '#default_value' => $this->configuration['level_high'],
      '#min' => 0,
      '#max' => 100,
      '#required' => TRUE,
    ];

    $form['colorize_amount'] = [
      '#type' => 'number',
      '#title' => $this->t('Grey Colorize Amount (%)'),
      '#description' => $this->t('Amount of grey colorization to apply'),
      '#default_value' => $this->configuration['colorize_amount'],
      '#min' => 0,
      '#max' => 100,
      '#required' => TRUE,
    ];

    $form['modulate_brightness'] = [
      '#type' => 'number',
      '#title' => $this->t('Brightness Modulation'),
      '#description' => $this->t('Brightness adjustment (100 is normal)'),
      '#default_value' => $this->configuration['modulate_brightness'],
      '#min' => 0,
      '#max' => 200,
      '#required' => TRUE,
    ];

    $form['modulate_saturation'] = [
      '#type' => 'number',
      '#title' => $this->t('Saturation Modulation'),
      '#description' => $this->t('Saturation adjustment (100 is normal)'),
      '#default_value' => $this->configuration['modulate_saturation'],
      '#min' => 0,
      '#max' => 200,
      '#required' => TRUE,
    ];

    $form['colors'] = [
      '#type' => 'number',
      '#title' => $this->t('Number of Colors'),
      '#description' => $this->t('Number of colors to reduce to'),
      '#default_value' => $this->configuration['colors'],
      '#min' => 2,
      '#max' => 256,
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    $this->configuration['level_low'] = $form_state->getValue('level_low');
    $this->configuration['level_high'] = $form_state->getValue('level_high');
    $this->configuration['colorize_amount'] = $form_state->getValue('colorize_amount');
    $this->configuration['modulate_brightness'] = $form_state->getValue('modulate_brightness');
    $this->configuration['modulate_saturation'] = $form_state->getValue('modulate_saturation');
    $this->configuration['colors'] = $form_state->getValue('colors');
  }

  /**
   * {@inheritdoc}
   */
  public function applyEffect(ImageInterface $image) {
    if (!$image->apply('dither', [
      'level_low' => $this->configuration['level_low'],
      'level_high' => $this->configuration['level_high'],
      'colorize_amount' => $this->configuration['colorize_amount'],
      'modulate_brightness' => $this->configuration['modulate_brightness'],
      'modulate_saturation' => $this->configuration['modulate_saturation'],
      'colors' => $this->configuration['colors'],
    ])) {
      $this->logger->error('Dither effect failed using the %toolkit toolkit on %path', [
        '%toolkit' => $image->getToolkitId(),
        '%path' => $image->getSource(),
      ]);
      return FALSE;
    }
    return TRUE;
  }

}
