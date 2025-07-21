<?php

namespace app\modules\lab\models;


use Yii;
use luya\bootstrap4\grid\GridView;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "examen".
 *
 * @property int $id
 * @property int $analisis_id
 * @property int $orden_id
 * @property string $precio
 * @property string $nota
 *
 * @property Analisis $analisis
 * @property Orden $orden
 * @property ExamenParametro[] $examenParametros
 * @property ExamenGermen[] $examenGermenes
 */
class Examen extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'examen';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['analisis_id', 'orden_id'], 'integer'],
            [['precio'], 'number'],
            [['_orden_cerrada'], 'boolean'],
            [['nota'], 'string', 'max' => 500],
            [['analisis_id'], 'exist', 'skipOnError' => true, 'targetClass' => Analisis::class, 'targetAttribute' => ['analisis_id' => 'id']],
            [['orden_id'], 'exist', 'skipOnError' => true, 'targetClass' => Orden::class, 'targetAttribute' => ['orden_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'analisis_id' => 'Analisis',
            'orden_id' => 'Orden',
            'precio' => 'Precio',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnalisis()
    {
        return $this->hasOne(Analisis::class, ['id' => 'analisis_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrden()
    {
        return $this->hasOne(Orden::class, ['id' => 'orden_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExamenParametros()
    {
        return $this->hasMany(ExamenParametro::class, ['examen_id' => 'id']);


    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExamenGermenes()
    {
        return $this->hasMany(ExamenGermen::class, ['examen_id' => 'id']);

    }
}