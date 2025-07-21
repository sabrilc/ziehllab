<?php

namespace app\modules\lab\models;

use Yii;

/**
 * This is the model class for table "parametro".
 *
 * @property int $id
 * @property string $descripcion
 * @property int $orden_impresion
 * @property string $valores_referencia_seleccionable
 * @property string $unico_valor_referencial
 * @property string $hombre_valo_de_referencia_min
 * @property string $hombre_valo_de_referencia_max
 * @property string $mujer_valo_de_referencia_max
 * @property string $mujer_valo_de_referencia_min
 * @property string $ninio_valo_de_referencia_max
 * @property string $ninio_valo_de_referencia_min
 * @property string $valores_posibles
 * @property int $seccion_id
 * @property int $metodo_id
 * @property int $medida_id
 * @property int $analisis_id
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property ExamenParametro[] $examenParametros
 * @property Metodo $metodo
 * @property Medida $medida
 * @property Analisis $analisis
 * @property Seccion $seccion
 */
class Parametro extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'parametro';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['orden_impresion', 'seccion_id', 'metodo_id', 'medida_id', 'analisis_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['descripcion'], 'string', 'max' => 200],
			[['limite_deteccion'], 'string', 'max' => 300],
            [['valores_referencia_seleccionable', 'unico_valor_referencial','ensayo','amplificacion_deteccion'], 'string', 'max' => 500],
            [['hombre_valo_de_referencia_min', 'hombre_valo_de_referencia_max', 'mujer_valo_de_referencia_max', 'mujer_valo_de_referencia_min', 'ninio_valo_de_referencia_max', 'ninio_valo_de_referencia_min'], 'string', 'max' => 20],
            [['valores_posibles'], 'string', 'max' => 3000],
            [['metodo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Metodo::class, 'targetAttribute' => ['metodo_id' => 'id']],
            [['medida_id'], 'exist', 'skipOnError' => true, 'targetClass' => Medida::class, 'targetAttribute' => ['medida_id' => 'id']],
            [['analisis_id'], 'exist', 'skipOnError' => true, 'targetClass' => Analisis::class, 'targetAttribute' => ['analisis_id' => 'id']],
            [['seccion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Seccion::class, 'targetAttribute' => ['seccion_id' => 'id']],
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
            'orden_impresion' => 'Orden de Impresión',
            'valores_referencia_seleccionable' => 'Valores Referencia Seleccionable',
            'unico_valor_referencial' => 'Unico Valor Referencial',
            'hombre_valo_de_referencia_min' => 'Hombre Valo De Referencia Min',
            'hombre_valo_de_referencia_max' => 'Hombre Valo De Referencia Max',
            'mujer_valo_de_referencia_max' => 'Mujer Valo De Referencia Max',
            'mujer_valo_de_referencia_min' => 'Mujer Valo De Referencia Min',
            'ninio_valo_de_referencia_max' => 'Ninio Valo De Referencia Max',
            'ninio_valo_de_referencia_min' => 'Ninio Valo De Referencia Min',
            'valores_posibles' => 'Valores Posibles',
			'limite_deteccion' => 'Límite de detección',
            'seccion_id' => 'Sección',
            'metodo_id' => 'Método',
            'medida_id' => 'Medida',
            'analisis_id' => 'Análisis',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExamenParametros()
    {
        return $this->hasMany(ExamenParametro::class, ['parametro_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMetodo()
    {
        return $this->hasOne(Metodo::class, ['id' => 'metodo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMedida()
    {
        return $this->hasOne(Medida::class, ['id' => 'medida_id']);
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
    public function getSeccion()
    {
        return $this->hasOne(Seccion::class, ['id' => 'seccion_id']);
    }
}
