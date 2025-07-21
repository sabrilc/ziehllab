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
class JQueryClockTimePickerAsset extends AssetBundle
{
    public $sourcePath = '@javaScript/node_modules/jquery-clock-timepicker';
    
   
  
    public $css = [
       
    ];
    
  
   
    public $js = [ 
        'jquery-clock-timepicker.min.js',
    ];
    public $depends = [
       'yii\bootstrap\BootstrapAsset',
    ];
}
