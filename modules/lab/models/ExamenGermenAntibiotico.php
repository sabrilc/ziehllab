<?php

namespace app\modules\lab\models;

use Yii;

/**
 * This is the model class for table "examen_germen_antibiotico".
 *
 * @property int $id
 * @property string $valor
 * @property string $tipo
 * @property int $antibiotico_id
 * @property int $examen_germen_id
 *
 * @property Antibiotico $antibiotico
 * @property ExamenGermen $examenGermen
 */
class ExamenGermenAntibiotico extends \yii\db\ActiveRecord
{
    public $descripcion;
/**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'examen_germen_antibiotico';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['antibiotico_id', 'examen_germen_id'], 'integer'],
            [['valor'], 'string', 'max' => 8],
            [['tipo'], 'string', 'max' => 15],
            [['antibiotico_id'], 'exist', 'skipOnError' => true, 'targetClass' => Antibiotico::class, 'targetAttribute' => ['antibiotico_id' => 'id']],
            [['examen_germen_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamenGermen::class, 'targetAttribute' => ['examen_germen_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'valor' => 'Valor',
            'tipo' => 'Tipo',
            'antibiotico_id' => 'Antibiotico ID',
            'examen_germen_id' => 'Examen Germen ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAntibiotico()
    {
        return $this->hasOne(Antibiotico::class, ['id' => 'antibiotico_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExamenGermen()
    {
        return $this->hasOne(ExamenGermen::class, ['id' => 'examen_germen_id']);
    }
}
