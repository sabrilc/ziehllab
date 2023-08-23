<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "seccion".
 *
 * @property int $id
 * @property string $descripcion
 * @property int $analisis_id
 *
 * @property Parametro[] $parametros
 * @property Analisis $analisis
 */
class Seccion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'seccion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['analisis_id'], 'integer'],
            [['descripcion'], 'string', 'max' => 100],
            [['analisis_id'], 'exist', 'skipOnError' => true, 'targetClass' => Analisis::className(), 'targetAttribute' => ['analisis_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descripcion' => 'Descripción',
            'analisis_id' => 'Análisis',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParametros()
    {
        return $this->hasMany(Parametro::className(), ['seccion_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnalisis()
    {
        return $this->hasOne(Analisis::className(), ['id' => 'analisis_id']);
    }
}
