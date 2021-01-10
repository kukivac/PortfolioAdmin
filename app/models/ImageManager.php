<?php

namespace app\models;

require("../vendor/autoload.php");

use app\config\ImageOptimizerConfig;
use Exception;
use Intervention\Image\ImageManagerStatic as Image;
use Intervention\Image\Exception\NotWritableException;
use RuntimeException;


/**
 * Manager ImageManager
 * pro více info http://image.intervention.io/getting_started/introduction
 *
 * @package app\models
 */
class ImageManager
{
    /**
     * Edits image to default parameters defined in ImageOptimizerConfig
     *
     * @param string $imgURL
     *
     * @return RuntimeException|void
     */
    static function defaultImage(string $imgURL)
    {
        try {
            self::editImage($imgURL,$imgURL);
        } catch (NotWritableException $exception) {
            return new RuntimeException;
        }
    }

    /**
     * Makes thumbnail version of image, by defined resolution in ImageOptimizerConfig
     *
     * @param string $imgURL
     *
     * @return RuntimeException|void
     */
    static function makeThumbnail(string $imgURL)
    {
        $newURL = "images/thumbnail/" . array_reverse(explode("/", $imgURL))[0];
        $oldURL = $imgURL;
        try {
            self::editImage($newURL,$oldURL);
        } catch (NotWritableException $exception) {
            return new RuntimeException;
        }
    }

    /**
     * @throws NotWritableException
     * @param $imgURL
     */
    static function editImage($newURL,$oldURL)
    {
        $img = Image::make($oldURL);

        $height = $img->height();
        $width = $img->width();
        //na šířku
        if ($width > $height) {
            if ($width > ImageOptimizerConfig::$defaultImageWidth) {
                $img->resize(ImageOptimizerConfig::$defaultImageWidth, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            } else {
                $img->save($newURL);
            }
        } //na výšku
        else {
            if ($height > ImageOptimizerConfig::$defaultImageHeight) {
                $img->resize(null, ImageOptimizerConfig::$defaultImageHeight, function ($constraint) {
                    $constraint->aspectRatio();
                });
            } else {
                $img->save($newURL);
            }
        }
        $img->save($newURL);
    }
}
