<?php

namespace app\modules\lab\models;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "analisis".
 *
 * @property int $id
 * @property string $codigo
 * @property string $nombre
 * @property string $descripcion
 * @property string $precio
 * @property int $activo
 * @property int $tipo_muestra_id
 * @property int $tipo_analisis_id
 * @property string $created_at
 * @property string $updated_at
 * @property int $hoja_impresion
 * @property int $orden_impresion
 * @property int $created_by
 * @property int $updated_by
 *
 * @property TipoMuestra $tipoMuestra
 * @property TipoAnalisis $tipoAnalisis
 * @property CotizacionAnalisis[] $cotizacionAnalises
 * @property Examen[] $examens
 * @property Parametro[] $parametros
 * @property Seccion[] $seccions
 */
class Analisis extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'analisis';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre','precio', 'tipo_muestra_id', 'tipo_analisis_id', 'hoja_impresion','orden_impresion'], 'required'],
            [['nombre','descripcion','precio', 'tipo_muestra_id', 'tipo_analisis_id', 'hoja_impresion','orden_impresion'], 'filter','filter'=>'strtoupper'],
            [['precio'], 'number'],
            [['activo', 'tipo_muestra_id', 'tipo_analisis_id', 'created_by', 'updated_by','hoja_impresion','orden_impresion'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['codigo'], 'string', 'max' => 10],
            [['nombre', 'acess_qr_text'], 'string', 'max' => 100],
            [['descripcion'], 'string', 'max' => 200],
            [['tipo_muestra_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoMuestra::class, 'targetAttribute' => ['tipo_muestra_id' => 'id']],
            [['tipo_analisis_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoAnalisis::class, 'targetAttribute' => ['tipo_analisis_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codigo' => 'Código',
            'nombre' => 'Nombre',
            'descripcion' => 'Descripción',
            'precio' => 'Precio',
            'activo' => 'Activo',
			'acess_qr_text' => 'Contenido del QR de la ACESS',
            'tipo_muestra_id' => 'Tipo Muestra',
            'tipo_analisis_id' => 'Tipo Análisis',
            'hoja_impresion'=>'Número de hoja en impresión',
            'orden_impresion'=>'Orden En Impresión',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoMuestra()
    {
        return $this->hasOne(TipoMuestra::class, ['id' => 'tipo_muestra_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoAnalisis()
    {
        return $this->hasOne(TipoAnalisis::class, ['id' => 'tipo_analisis_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCotizacionAnalises()
    {
        return $this->hasMany(CotizacionAnalisis::class, ['analisis_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExamens()
    {
        return $this->hasMany(Examen::class, ['analisis_id' => 'id']);
    }

    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParametros()
    {
        return $this->hasMany(Parametro::class, ['analisis_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */

    public function getParametrosParaIngresoResultado()
    {
        return $this->hasMany(Parametro::class, ['analisis_id' => 'id'])
            ->orderBy([
                new Expression('"seccion_id" ASC NULLS FIRST'),
                new Expression('"orden_impresion" ASC'),
                new Expression('"id" ASC'),
            ]);
    }
    /*->orderBy([ 'seccion_id' => SORT_ASC, 'orden_impresion' => SORT_ASC, 'id'=> SORT_ASC ]);*/

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeccions()
    {
        return $this->hasMany(Seccion::class, ['analisis_id' => 'id']);
    }
    

    public function getNombreCosto()
    {
        return $this->nombre.' ('.$this->precio.')';
    }

}
