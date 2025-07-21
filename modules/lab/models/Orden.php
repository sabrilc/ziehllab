<?php

namespace app\modules\lab\models;

use app\modules\site\models\User;
use common\Tools;
use Yii;
use yii\db\Exception;
use yii\db\Expression;

/**
 * This is the model class for table "orden".
 *
 * @property int $id
 * @property string $codigo
 * @property string $codigo_secreto
 * @property string $fecha
 * @property string $precio
 * @property string $descuento
 * @property string $valor_total
 * @property string $abono
 * @property int $pagado
 * @property int $cerrada
 * @property int $paciente_id
 * @property int $doctor_id
 * @property int $laboratorista_id
 * @property int $responsable_tecnico_id
 * @property int $cotizacion_id
 * @property int $email_enviado
 * @property string $fecha_email_enviado
 * @property string $token
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property  bool $firmado_digitalmente
 * @property  string $fecha_firmado_digital
 *
 * @property Cotizacion[] $cotizacions
 * @property Examen[] $examens
 * @property User $paciente
 * @property User $doctor
 * @property Cotizacion $cotizacion
 */
class Orden extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orden';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fecha', 'fecha_email_enviado', 'created_at', 'updated_at','fecha_resultados','hora_resultados','_id','_examenes'], 'safe'],
            [['precio', 'descuento', 'valor_total', 'abono', 'porcentaje_desc'], 'number'],
            [['pagado', 'cerrada','paciente_id', 'doctor_id', 'laboratorista_id', 'responsable_tecnico_id','cotizacion_id', 'email_enviado', 'created_by', 'updated_by'], 'integer'],
            [['codigo'], 'string', 'max' => 10],
            [['codigo_secreto'], 'string', 'max' => 6],
            [['token'], 'string', 'max' => 100],
            [['firmado_digitalmente'], 'boolean'],
            [['paciente_info', 'solicitante_info'], 'string', 'max' => 100],
            [['paciente_id'], 'required', 'on' => 'registro'],
            [['paciente_id', 'laboratorista_id', 'responsable_tecnico_id'], 'required', 'on' => 'nuevo'],
            [['paciente_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['paciente_id' => 'id']],           
            [['doctor_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['doctor_id' => 'id']],
            [['cotizacion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cotizacion::class, 'targetAttribute' => ['cotizacion_id' => 'id']],
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
            'fecha' => 'Fecha',
            'precio' => 'Precio',
            'descuento' => 'Descuento',
            'valor_total' => 'Valor Total',
            'abono' => 'Abono',
            'pagado' => 'Pagado',
            'cerrada' => 'Cerrada',
            'paciente_id' => 'Paciente',
            'responsable_tecnico_id'=>'Responsable Técnico',
            'doctor_id' => 'Doctor',
            'cotizacion_id' => 'Cotizacion',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'laboratorista_id'=>'Laboratorista',
            'paciente_info'=>'Información del paciente',
            'solicitante_info'=>'Información del solictante de la prueba',
         
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCotizacions()
    {
        return $this->hasMany(Cotizacion::class, ['orden_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExamens()
    {
        return $this->hasMany(Examen::class, ['orden_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCultivos()
    {
    return $this->getExamens()->joinWith(['analisis'])->where(['analisis.tipo_analisis_id'=>11]);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCotizacion()
    {
        return $this->hasOne(Cotizacion::class, ['id' => 'cotizacion_id']);
    }

   
}
