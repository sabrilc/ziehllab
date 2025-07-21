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
class OverlayJsAsset extends AssetBundle
{
    public $sourcePath = '@javaScript/lib/overlayjs-1.0.5';
    
   
  
    public $css = [
       'overlay.css'
    ];
    
  
   
    public $js = [ 
        'overlay.js',
    ];
    public $depends = [
       'yii\bootstrap\BootstrapAsset',
    ];
}
