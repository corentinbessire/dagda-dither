# Dagda Dither

A configurable **dithering and color-reduction image effect** for Drupal 10 & 11,
powered by [ImageMagick](https://imagemagick.org/). Add it to any image style to
give photographs a retro, posterized, ordered-dither look — useful for stylised
hero images, halftone-style thumbnails, or a consistent reduced-palette art
direction across a site.

The effect chains a level adjustment, a grey colorize pass, brightness/saturation
modulation, color reduction, and an ordered dither into a single image-style step.

## Requirements

- Drupal 10 or 11
- [ImageMagick module](https://www.drupal.org/project/imagemagick) (`drupal/imagemagick`)
- The `convert` binary from ImageMagick (or GraphicsMagick) installed on the server,
  with Drupal's image toolkit set to **ImageMagick** at
  `/admin/config/media/image-toolkit`

## Installation

Install with Composer (recommended):

```bash
composer require corentinbessire/dagda-dither
drush en dagda_dither
```

If the package is not on Packagist, add this repository to your project's
`composer.json` first:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/corentinbessire/dagda-dither"
        }
    ]
}
```

Then run `composer require corentinbessire/dagda-dither` as above. The module
installs into `web/modules/contrib/dagda_dither` and pulls in `drupal/imagemagick`
automatically.

## Usage

1. Make sure the image toolkit is set to **ImageMagick** at
   `/admin/config/media/image-toolkit`.
2. Go to **Configuration → Media → Image styles**
   (`/admin/config/media/image-styles`).
3. Edit or create an image style, choose **Dither Effect** from the
   *Effect* select, and click **Add**.
4. Tune the parameters (see below) and save.
5. Use the image style anywhere on the site (field formatters, view modes,
   responsive image mappings, etc.). The effect is applied when the derivative
   is generated.

## Effect parameters

| Parameter | Range | Default | Description |
|---|---|---|---|
| **Level Low (%)** | 0–100 | 0 | Lower threshold for the `-level` adjustment. |
| **Level High (%)** | 0–100 | 95 | Upper threshold for the `-level` adjustment. |
| **Grey Colorize Amount (%)** | 0–100 | 10 | How much grey is mixed into the image (`-fill grey -colorize`). |
| **Brightness Modulation** | 0–200 | 115 | Brightness adjustment; `100` is unchanged. |
| **Saturation Modulation** | 0–200 | 90 | Saturation adjustment; `100` is unchanged. |
| **Number of Colors** | 2–256 | 64 | Target palette size for color reduction (`-colors`). |

> **Tip:** keep *Level Low* below *Level High*. Inverting them (e.g. low `95`,
> high `0`) tells ImageMagick to invert the image's levels, which is rarely what
> you want.

## How it works

The module ships two plugins:

- **`DitherImageEffect`** (`Drupal\dagda_dither\Plugin\ImageEffect\DitherImageEffect`,
  plugin id `dither`) — a `ConfigurableImageEffectBase` that exposes the form
  above and applies the `dither` toolkit operation.
- **`Dither`** (`Drupal\dagda_dither\Plugin\ImageToolkit\Operation\imagemagick\Dither`,
  operation `dither`) — the ImageMagick implementation that validates the
  arguments and appends the `convert` flags.

For a given configuration the effect runs the equivalent of:

```bash
convert input.jpg \
  -level <level_low>%,<level_high>% \
  -fill grey -colorize <colorize_amount>% \
  -modulate <modulate_brightness>,<modulate_saturation> \
  -colors <colors> \
  -ordered-dither o2x2,4 \
  output.jpg
```

## License

GPL-2.0-or-later. See [LICENSE](LICENSE).
