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
class PaperThemeAsset extends AssetBundle
{
    public $sourcePath = '@themes/Paper';
    
   
  
    public $css = [
        'css/bootstrap.css',
        'css/paper.css',
        'css/site.css',
    ];
    public $js = [  ];
    public $depends = [
       'app\assets\MdiAsset',
       'yii\web\YiiAsset',
       'yii\bootstrap\BootstrapAsset',
       'app\assets\SweetAlertAsset',
    ];
}
