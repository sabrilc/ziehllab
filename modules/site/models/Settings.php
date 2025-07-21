<?php

namespace app\modules\site\models;

use Yii;

/**
 * This is the model class for table "settings".
 *
 * @property int $id
 * @property string $site_name
 * @property string $address
 * @property string $telefono
 * @property string $email
 * @property string $primary_color
 * @property string $secondary_color
 */
class Settings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['site_name', 'address', 'telefono', 'email', 'primary_color', 'secondary_color'], 'required'],
            [['site_name', 'address'], 'string', 'max' => 200],
            [['telefono'], 'string', 'max' => 100],
            [['email'], 'string', 'max' => 50],
            [['primary_color', 'secondary_color'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'site_name' => 'Site Name',
            'address' => 'Address',
            'telefono' => 'Telefono',
            'email' => 'Email',
            'primary_color' => 'Primary Color',
            'secondary_color' => 'Secondary Color',
        ];
    }
}
