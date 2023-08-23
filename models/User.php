<?php

namespace app\models;
use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
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
 */

class User extends ActiveRecord implements IdentityInterface
{
    public $_password;
    public $_rol;
    public $_descripcion;

    

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
            [['username', 'nombres','edad','sexo_id'],'required'],
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
    
    /**
     * {@inheritdoc}
     */ 
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Usuario',
            'email' => 'Email',
            'sexo_id' => 'Sexo',
            'edad' => 'Edad',
            'unidad_tiempo' => 'Edad en',
            'auth_key' => 'Auth Key',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'password' => 'Clave',
            '_password' => 'Confirmacion del clave',
            'nombres' => 'Nombres',           
            'identificacion' => 'Identificacion',
        ];
    }
    
    public function getOrdenesPaciente()
    {
        return $this->hasMany(Orden::className(), ['paciente_id' => 'id']);
    }
    
    public function getOrdenesMedico()
    {
        return $this->hasMany(Orden::className(), ['doctor_id' => 'id']);
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
     * Finds an identity by the given ID.
     *
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }
    
    /**
     * Finds an identity by the given User name.
     *
     * @param string  UserName to be looked for
     * @return IdentityInterface|null the identity object that matches the given userName.
     */
    public static function findByUsername($userName)
    {
        return static::findOne([
            'username' => $userName,
            'activo' => true
        ]);
    }

    public static function findByEmail($userEmail)
    {
        return static::findOne([
            'email' => $userEmail,
            'activo' => true
        ]);
    }
    
    
  
    
    
    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }
    
    
        
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function getFullName(){
        return $this->username.'|'.$this->nombres;
        
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return bool if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
    
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->auth_key = Yii::$app->security->generateRandomString();
            }
            if (!empty($this->password)) {
                $this->password = Yii::$app->getSecurity()->generatePasswordHash($this->password);
            } else {
                unset($this->password);
            }
            return true;
        }
        return false;
    }


    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
		if( is_null($this->password)){
			return false;
		}
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    public function setPassword($password)
    {
        $this->password = trim($password);
    }
    

    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
        return static::findOne([
            'password_reset_token' => $token,
            'activo' => true,
        ]);
    }
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
    
 
}
