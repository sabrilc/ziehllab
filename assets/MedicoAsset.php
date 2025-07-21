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
class MedicoAsset extends AssetBundle
{
    public $sourcePath = '@javaScript/pages';
    
    public $css = [ ];
       
    public $js = [ 
        'medico.js', 
    ];
    public $depends = [
        'app\assets\PaperThemeAsset'
    ];
}
