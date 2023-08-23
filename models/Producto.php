<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "producto".
 *
 * @property int $id
 * @property string $descripcion
 * @property int $id_periodo
 * @property string $gasto
 * @property string $ingreso
 * @property string $ganancia
 * @property string $valor_control
 *
 * @property DetControl[] $detControls
 * @property DetGasto[] $detGastos
 * @property DetIngreso[] $detIngresos
 * @property Periodo $periodo
 */
class Producto extends \yii\db\ActiveRecord
{
    
    public $_enviar;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'producto';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['_enviar'], 'safe'],
            [['id_periodo'], 'integer'],
            [['gasto', 'ingreso', 'ganancia', 'valor_control'], 'number'],
            [['descripcion'], 'string', 'max' => 255],
            [['id_periodo','descripcion'], 'required'],
            [['id_periodo'], 'exist', 'skipOnError' => true, 'targetClass' => Periodo::className(), 'targetAttribute' => ['id_periodo' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descripcion' => 'Descripcion',
            'id_periodo' => 'Periodo',
            'gasto' => 'Gasto',
            'ingreso' => 'Ingreso',
            'ganancia' => 'Ganancia',
            'valor_control' => 'Valor Control',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getControles()
    {
        return $this->hasMany(Control::className(), ['id_producto' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGastos()
    {
        return $this->hasMany(Gasto::className(), ['id_producto' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIngresos()
    {
        return $this->hasMany(Ingreso::className(), ['id_producto' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeriodo()
    {
        return $this->hasOne(Periodo::className(), ['id' => 'id_periodo']);
    }
    
    public function calcularValores() {
        
        $this->gasto=$this->getGastos()->sum('valor');
        $this->ingreso=$this->getIngresos()->sum('valor');
        $this->valor_control=$this->getControles()->sum('valor');
        $this->ganancia=$this->ingreso-($this->gasto+$this->valor_control);
        $this->save(false);
        
    }
}
