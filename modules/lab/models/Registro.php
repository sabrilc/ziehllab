<?php
namespace app\modules\lab\models;
use yii\db\Expression;
use Yii;
class Registro
{
    public static function onCreated($model) {
        $model->created_at= new Expression('now()');
        $model->created_by= Yii::$app->user->identity->id;
        $model->save(false);
    }
    
    public static function onUpdated($model) {
        $model->updated_at= new Expression('now()');
        $model->updated_by= Yii::$app->user->identity->id;
        $model->save(false);
    }
    
}

