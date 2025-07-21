<?php

namespace app\modules\lab\models;

use Yii;

/**
 * This is the model class for table "antibiotico".
 *
 * @property int $id
 * @property string $descripcion
 *
 * @property ExamenGermenAntibiotico[] $examenGermenAntibioticos
 */
class Antibiotico extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'antibiotico';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descripcion'], 'string', 'max' => 300],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descripcion' => 'Nombre',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExamenGermenAntibioticos()
    {
        return $this->hasMany(ExamenGermenAntibiotico::class, ['antibiotico_id' => 'id']);
    }
}
