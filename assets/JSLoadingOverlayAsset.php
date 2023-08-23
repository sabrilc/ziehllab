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
class JSLoadingOverlayAsset extends AssetBundle
{
    public $sourcePath = '@javaScript/node_modules/js-loading-overlay/dist/';
    
   
  
    public $css = [
       
    ];
    
  
   
    public $js = [ 
        'js-loading-overlay.min.js',   
    ];
    public $depends = [
        'app\assets\PaperThemeAsset',
    ];
}
