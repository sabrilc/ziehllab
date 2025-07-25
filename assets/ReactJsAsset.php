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
class ReactJsAsset extends AssetBundle
{
    public $sourcePath = '@javaScript/node_modules/';
    
    public $css = [ ];
       
    public $js = [ 
        'react/umd/react.development.js',
        'react-dom/umd/react-dom.development.js',
        'axios/dist/axios.min.js',
        '@babel/standalone/babel.min.js'
    ];
    public $depends = [
        'app\assets\PaperThemeAsset'
    ];
}
