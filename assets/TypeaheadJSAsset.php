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
class TypeaheadJSAsset extends AssetBundle
{
    public $sourcePath = '@javaScript/node_modules/typeahead.js/dist';
    
   
  
    public $css = [
       
    ];
    
   
    public $js = [ 
        'typeahead.jquery.min.js',
        
    ];
    public $depends = [
       'app\assets\PaperThemeAsset',
    ];
}
