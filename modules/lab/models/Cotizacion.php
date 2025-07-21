<?php

namespace app\modules\lab\models;

use Yii;

/**
 * This is the model class for table "cotizacion".
 *
 * @property int $id
 * @property string $codigo
 * @property string $nombres
 * @property string $apellidos
 * @property string $email
 * @property string $telefono
 * @property string $total
 * @property int $vigente
 * @property string $fecha
 * @property int $vista
 * @property int $orden_id
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property Orden $orden
 * @property CotizacionAnalisis[] $cotizacionAnalises
 * @property Orden[] $ordens
 */
class Cotizacion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cotizacion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['total'], 'number'],
            [['vigente', 'vista', 'orden_id',  'created_by', 'updated_by'], 'integer'],
            [['fecha', 'created_at', 'updated_at'], 'safe'],
            [['nombres', 'apellidos', 'email'], 'required'],
            [['nombres', 'apellidos', 'email'],'filter', 'filter'=>'strtoupper'],
            [['codigo'], 'string', 'max' => 10],
            [['nombres', 'apellidos'], 'string', 'max' => 200],
            [['email', 'telefono'], 'string', 'max' => 100],
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
            'codigo' => 'Codigo',
            'nombres' => 'Nombres',
            'apellidos' => 'Apellidos',
            'email' => 'Email',
            'telefono' => 'Telefono',
            'total' => 'Total',
            'vigente' => 'Vigente',
            'fecha' => 'Fecha',
            'vista' => 'Vista',
            'orden_id' => 'Orden ID',           
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
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
    public function getCotizacionAnalises()
    {
        return $this->hasMany(CotizacionAnalisis::class, ['cotizacion_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdens()
    {
        return $this->hasMany(Orden::class, ['cotizacion_id' => 'id']);
    }
}
