<?php

namespace app\modules\lab\controllers;

use app\models\Examen;
use app\models\HistoriaGrid;
use app\models\Orden;
use app\models\PDF_HISTORIAL;
use app\models\User;
use Yii;
use yii\db\Expression;
use yii\web\Controller;
use yii\web\Response;


/**
 * UserController implements the CRUD actions for User model.
 */
class DocumentosController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['analisis', 'orden'],
                        'allow' => true,
                        'roles' => ['?','@'],
                    ],                   
                ],
            ],
        ];
    }

/**
 *  lista todo los usuarios
 */
    public function actionAnalisis($token='')
    {
      
        if( strlen( $token) > 50 ){           
            \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            $orden= Orden::findOne(['token'=> $token]);
            if( !is_null( $orden )) {
                return $orden->pdf();
            }
        }
        $this->layout = false;
        return $this->render('no_found');
        
       
    }

    public function actionOrden($token='')
    {

        if( strlen( $token) > 50 ){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            $orden= Orden::findOne(['token'=> $token]);
            if( !is_null( $orden )) {
                return $orden->pdf();
            }
        }
        $this->layout = false;
        return $this->render('no_found');


    }
    
   
    
  
    


}
