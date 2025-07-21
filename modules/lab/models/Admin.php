<?php

namespace app\modules\lab\models;

class Admin extends User
{
    public $_password;
    public $_rol;



   public function rules()
    {
        return [
            [['username', 'nombres'],'required'],
            [['nombres', 'email'],'filter', 'filter'=>'strtoupper'],
            [['password',],'required','on'=>'create'],
            [['username', 'email'],'unique'],
            [['edad'],'number','max' => 110,'min'=>0],
            [['created_at', 'updated_at','_rol'], 'safe'],
            [['activo','unidad_tiempo','edad','sexo_id'], 'default', 'value' => null],
            [['activo'], 'boolean'],
            [['username', 'email','password_reset_token'],'string', 'max' => 100],
            [['unidad_tiempo',],'string', 'max' => 5],
            [['auth_key', 'password', 'nombres' ], 
                'string', 'max' => 255],
            [['_password'], 'compare', 'compareAttribute' => 'password'],           
            [['identificacion'], 'string', 'max' => 10],
           
        ];
    }
    

    
 
}
