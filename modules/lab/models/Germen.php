<?php

namespace app\modules\lab\models;


/**
 * This is the model class for table "germen".
 *
 * @property int $id
 * @property string $descripcion

 */
class Germen extends \yii\db\ActiveRecord
{
    /**
     * Variables creadas para cargar datos temporales en la consuta de prueba de sensibilidad bacteriana;
     */
    public  $valor;
    public  $tipo;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'germen';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descripcion'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descripcion' => 'Descripcion',
        ];
    }
    
  
}
