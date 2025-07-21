<?php

namespace app\modules\lab\models;

use Yii;

/**
 * This is the model class for table "examen_parametro".
 *
 * @property int $id
 * @property string $valor
 * @property string $medida
 * @property string $referencia
 * @property int $examen_id
 * @property int $parametro_id
 *
 * @property Examen $examen
 * @property Parametro $parametro
 */
class ExamenParametro extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'examen_parametro';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['examen_id', 'parametro_id'], 'integer'],
            [['valor','medida'], 'string', 'max' => 100],
            [['referencia'], 'string', 'max' => 500],
            [['examen_id'], 'exist', 'skipOnError' => true, 'targetClass' => Examen::class, 'targetAttribute' => ['examen_id' => 'id']],
            [['parametro_id'], 'exist', 'skipOnError' => true, 'targetClass' => Parametro::class, 'targetAttribute' => ['parametro_id' => 'id']],
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
            'medida' => 'Medida',
            'referencia' => 'Referencia',
            'examen_id' => 'Examen ID',
            'parametro_id' => 'Parametro ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExamen()
    {
        return $this->hasOne(Examen::class, ['id' => 'examen_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParametro()
    {
        return $this->hasOne(Parametro::class, ['id' => 'parametro_id']);
    }
}
