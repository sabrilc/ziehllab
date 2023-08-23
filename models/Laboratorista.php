<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "laboratorista".
 *
 * @property int $id
 * @property string $nombres
 * @property string $cargo
 * @property string $registro_msp
 * @property string $registro_senescyt
 * @property int $dbremove
 */
class Laboratorista extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laboratorista';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dbremove'], 'integer'],
            [['nombres', 'cargo', 'registro_msp', 'registro_senescyt'], 'string', 'max' => 200],
			[['identificacion'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
			'identificacion' => 'Identificación',
            'nombres' => 'Nombres',
            'cargo' => 'Cargo',
            'registro_msp' => 'Registro Msp',
            'registro_senescyt' => 'Registro Senescyt',
            'dbremove' => 'Dbremove',
        ];
    }
}
