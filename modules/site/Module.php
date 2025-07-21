<?php

namespace app\modules\site;

/**
 * site module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\site\controllers';
    /**
     * {@inheritdoc}
     */
    public $defaultRoute = 'site'; // <- Aquí defines tu controlador por defecto


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
