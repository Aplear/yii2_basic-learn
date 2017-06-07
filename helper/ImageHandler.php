<?php

namespace app\helper;

use Yii;
use yii\imagine\Image as Imagine;
use Imagine\Image\Box;

class ImageHandler
{
    public static $height = 250;
    public static $width = 200;

    /**
     * server image path
     * @return bool|string
     */
    public static function getPath($pathname=false)
    {
        if($pathname !== false) {
            return Yii::getAlias('@app/web/images/'.$pathname.'/');
        }
    }

    public static function rateablyResize($imagePath)
    {

        $img = Imagine::getImagine()->open($imagePath);

        $size = $img->getSize();
        $ratio = $size->getWidth()/$size->getHeight();

        if (self::$width/self::$height > $ratio) {
            self::$width = round(self::$height*$ratio);
        } else {
            self::$height = round(self::$width/$ratio);
        }

        $box = new Box(self::$width, self::$height);
        $img->resize($box)->save($imagePath);

    }
}