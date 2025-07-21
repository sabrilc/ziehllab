<?php

namespace app\modules\lab\models;

use Yii;

/**
 * This is the model class for table "secretaria".
 *
 * @property int $id
 * @property string $nombres
 * @property string $cargo
 * @property int $dbremove
 */
class Secretaria extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'secretaria';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dbremove'], 'integer'],
            [['nombres', 'cargo'], 'string', 'max' => 300],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombres' => 'Nombres',
            'cargo' => 'Cargo',
            'dbremove' => 'Dbremove',
        ];
    }
}
