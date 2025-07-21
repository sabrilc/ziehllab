<?php

namespace app\modules\lab\models;

use Yii;

/**
 * This is the model class for table "cotizacion_analisis".
 *
 * @property int $id
 * @property int $cotizacion_id
 * @property int $analisis_id
 * @property string $precio
 *
 * @property Cotizacion $cotizacion
 * @property Analisis $analisis
 */
class CotizacionAnalisis extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cotizacion_analisis';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cotizacion_id', 'analisis_id'], 'integer'],
            [['precio'], 'number'],
            [['cotizacion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cotizacion::class, 'targetAttribute' => ['cotizacion_id' => 'id']],
            [['analisis_id'], 'exist', 'skipOnError' => true, 'targetClass' => Analisis::class, 'targetAttribute' => ['analisis_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cotizacion_id' => 'Cotizacion ID',
            'analisis_id' => 'Analisis ID',
            'precio' => 'Precio',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCotizacion()
    {
        return $this->hasOne(Cotizacion::class, ['id' => 'cotizacion_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnalisis()
    {
        return $this->hasOne(Analisis::class, ['id' => 'analisis_id']);
    }
}
