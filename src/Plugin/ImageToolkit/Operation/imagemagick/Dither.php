<?php

namespace Drupal\dagda_dither\Plugin\ImageToolkit\Operation\imagemagick;

use Drupal\Core\ImageToolkit\Attribute\ImageToolkitOperation;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\imagemagick\ArgumentMode;
use Drupal\imagemagick\Plugin\ImageToolkit\Operation\imagemagick\ImagemagickImageToolkitOperationBase;

/**
 * Provides a Dithered effect using  ImageMagick integration toolkit.
 */
#[ImageToolkitOperation(
  id: "dithered_effect",
  toolkit: "imagemagick",
  operation: "dither",
  label: new TranslatableMarkup("Dithered Effect"),
  description: new TranslatableMarkup("Apply dithering and color reduction effect with specific levels and modulation.")
)]
class Dither extends ImagemagickImageToolkitOperationBase {

  /**
   * {@inheritdoc}
   */
  protected function arguments() {
    return [
      'level_low' => [
        'description' => 'Lower threshold for level adjustment',
        'required' => TRUE,
        'default' => 0,
      ],
      'level_high' => [
        'description' => 'Upper threshold for level adjustment',
        'required' => TRUE,
        'default' => 95,
      ],
      'colorize_amount' => [
        'description' => 'Amount of grey colorization',
        'required' => TRUE,
        'default' => 10,
      ],
      'modulate_brightness' => [
        'description' => 'Brightness modulation',
        'required' => TRUE,
        'default' => 115,
      ],
      'modulate_saturation' => [
        'description' => 'Saturation modulation',
        'required' => TRUE,
        'default' => 90,
      ],
      'colors' => [
        'description' => 'Number of colors',
        'required' => TRUE,
        'default' => 64,
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function validateArguments(array $arguments) {
    // Validate level arguments.
    if ($arguments['level_low'] < 0 || $arguments['level_low'] > 100) {
      throw new \InvalidArgumentException("The level_low argument must be between 0 and 100");
    }
    if ($arguments['level_high'] < 0 || $arguments['level_high'] > 100) {
      throw new \InvalidArgumentException("The level_high argument must be between 0 and 100");
    }

    // Validate colorize amount.
    if ($arguments['colorize_amount'] < 0 || $arguments['colorize_amount'] > 100) {
      throw new \InvalidArgumentException("The colorize_amount argument must be between 0 and 100");
    }

    // Validate modulate arguments.
    if ($arguments['modulate_brightness'] < 0 || $arguments['modulate_brightness'] > 200) {
      throw new \InvalidArgumentException("The modulate_brightness argument must be between 0 and 200");
    }
    if ($arguments['modulate_saturation'] < 0 || $arguments['modulate_saturation'] > 200) {
      throw new \InvalidArgumentException("The modulate_saturation argument must be between 0 and 200");
    }

    // Validate colors.
    if ($arguments['colors'] < 2 || $arguments['colors'] > 256) {
      throw new \InvalidArgumentException("The colors argument must be between 2 and 256");
    }

    return $arguments;
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(array $arguments) {
    return $this->getToolkit()->arguments()->add([
      '-level',
      $arguments['level_low'] . '%,' . $arguments['level_high'] . '%',
      '-fill',
      'grey',
      '-colorize',
      $arguments['colorize_amount'] . '%',
      '-modulate',
      $arguments['modulate_brightness'] . ',' . $arguments['modulate_saturation'],
      '-colors',
      (string) $arguments['colors'],
      '-ordered-dither',
      'o2x2,4',
    ], ArgumentMode::PostSource);

  }

}
