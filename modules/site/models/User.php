<?php

namespace app\modules\site\models;
use app\modules\lab\models\Orden;
use app\modules\lab\models\Sexo;
use Yii;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $codigo
 * @property string $identificacion
 * @property string $nombres
 * @property int $edad
 * @property string $unidad_tiempo
 * @property int $sexo_id
 * @property string $telefono
 * @property int $activo
 * @property string $auth_key
 * @property string $password_reset_token
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property bool $activo
 * @property Orden[] $ordens
 * @property Sexo $sexo
 */

class User extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        return [
            [['username', 'nombres','sexo_id'],'required'],
            [['nombres', 'email','email_notificacion'],'filter', 'filter'=>'strtoupper'],
            [['password',],'required','on'=>'create'],
            [['username', 'email'],'unique'],
            [['edad'],'number','max' => 110,'min'=>0],
            [['created_at', 'updated_at','_rol'], 'safe'],
            [['activo','unidad_tiempo'], 'default', 'value' => null],
            [['activo'], 'boolean'],
            [['username', 'email','password_reset_token'],'string', 'max' => 100],
            [['direccion'],'string', 'max' => 500],

            [['unidad_tiempo',],'string', 'max' => 5],
            [['auth_key', 'password', 'nombres' ],
                'string', 'max' => 255],
            //  ['password','passwordRules'],
            [['_password'], 'compare', 'compareAttribute' => 'password'],
            [['identificacion'], 'string', 'max' => 10],
            [['sexo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sexo::class, 'targetAttribute' => ['sexo_id' => 'id']],

        ];
    }

    public function passwordRules()
    {
        if(!empty($this->password)){
            if(strlen($this->password)<8){
                $this->addError('password','El password ingresado debe ser de al menos 8 caracteres, 1 numero y letras.');
            }
            else{
                if(!preg_match('/[0-9]/',$this->password)){
                    $this->addError('password','Password debe contener un numero.');
                }
                if(!preg_match('/[a-zA-Z]/', $this->password)){
                    $this->addError('password','Password debe contener letras.');
                }
            }
        }
    }

    public function getOrdenesPaciente()
    {
        return $this->hasMany(Orden::class, ['paciente_id' => 'id']);
    }
    
    public function getOrdenesMedico()
    {
        return $this->hasMany(Orden::class, ['doctor_id' => 'id']);
    }
    
    
    
    public function getNombreUsuario()
    {
         return $this->nombres.' | '.$this->username;
    }
    public function getNombreCompleto()
    {
         return $this->nombres;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSexo()
    {
        return $this->hasOne(Sexo::class, ['id' => 'sexo_id']);
    }
   

    
    

    
        

    
 
}
