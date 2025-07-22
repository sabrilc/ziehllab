<?php

namespace app\modules\site\bussines;
use app\modules\lab\models\Orden;
use app\modules\site\models\User;
use Yii;
use yii\web\IdentityInterface;



class UserBussines extends User implements IdentityInterface
{
    public $_password;
    public $_rol;
    public $_descripcion;

    



    public function getEdad()
    {
        // Valida si la fecha de nacimiento está vacía
        if (empty($this->fecha_nacimiento)) {
            return 'N/A';
        }

        try {
            $fechaNacimiento = new \DateTime($this->fecha_nacimiento);
            $fechaActual = new \DateTime(); // Fecha actual

            $diferencia = $fechaNacimiento->diff($fechaActual);

            // Calcula la edad en años
            if ($diferencia->y > 0) {
                return $diferencia->y . ' años';
            }
            // Calcula la edad en meses si no tiene años
            elseif ($diferencia->m > 0) {
                return $diferencia->m . ' meses';
            }
            // Calcula la edad en días si no tiene años ni meses
            else {
                // Asegúrate de que $diferencia->d es el número de días transcurridos
                // Si la fecha de nacimiento es hoy o en el futuro cercano (caso raro para edad),
                // esto podría dar 0 o un número pequeño de días.
                return $diferencia->d . ' días';
            }
        } catch (\Exception $e) {
            // Manejo de errores si la fecha de nacimiento no es un formato válido
            Yii::error("Error al calcular la edad para el paciente {$this->id}: " . $e->getMessage());
            return 'N/A';
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

    public function getNombresConCedula()
    {
        return "($this->identificacion) $this->nombres";
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


    public function getId()
    {
       return $this->id;
    }
}
