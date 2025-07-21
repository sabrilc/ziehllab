<?php

namespace app\modules\site\models;

use Yii;

/**
 * This is the model class for table "empresa".
 *
 * @property int $id
 * @property string $razon_social
 * @property string $nombre_comercial
 * @property string $ruc
 * @property string $direccion
 * @property string $telefono
 */
class Empresa extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'empresa';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['razon_social'], 'required'],
            [['razon_social', 'nombre_comercial', 'telefono'], 'string', 'max' => 100],
            [['ruc'], 'string', 'max' => 13],
            [['direccion'], 'string', 'max' => 400],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'razon_social' => 'Razon Social',
            'nombre_comercial' => 'Nombre Comercial',
            'ruc' => 'Ruc',
            'direccion' => 'Direccion',
            'telefono' => 'Telefono',
        ];
    }
}
