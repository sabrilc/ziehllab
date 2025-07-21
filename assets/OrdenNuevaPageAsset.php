<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class OrdenNuevaPageAsset extends AssetBundle
{
    public $sourcePath = '@javaScript';
    
    public $css = [ 
        'css/orden-nueva.css', 
    ];
         
    public $js = [ 
        'pages/orden-nueva.js', 
    ];
    public $depends = [
        'app\assets\PaperThemeAsset'
    ];
}
