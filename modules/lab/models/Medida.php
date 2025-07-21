<?php
namespace app\modules\lab\models;

use Yii;

/**
 * This is the model class for table "medida".
 *
 * @property int $id
 * @property string $descripcion
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property Parametro[] $parametros
 */
class Medida extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'medida';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'safe'],
            [['descripcion', ], 'required'],
            [['created_by', 'updated_by'], 'integer'],
            [['descripcion'], 'string', 'max' => 20],
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
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParametros()
    {
        return $this->hasMany(Parametro::class, ['medida_id' => 'id']);
    }
}
