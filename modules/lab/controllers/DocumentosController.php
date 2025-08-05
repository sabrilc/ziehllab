<?php

namespace app\modules\lab\controllers;

use app\models\Examen;
use app\models\HistoriaGrid;
use app\models\Orden;
use app\models\PDF_HISTORIAL;
use app\models\User;
use app\modules\lab\bussines\OrdenBussines;
use app\modules\lab\pdfs\PDF_ORDEN_NO_PAGADA;
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
                        'actions' => ['orden','online-orden'],
                        'allow' => true,
                        'roles' => ['?','@'],
                    ],                   
                ],
            ],
        ];
    }

    public function actionOrden($token='')
    {

        if( strlen( $token) > 50 ){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            $orden= OrdenBussines::findOne(['token'=> $token]);
            if( !is_null( $orden )) {
                return $this->render( 'orden',['model'=>$orden, 'paciente'=>$orden->getPaciente()] );
            }
        }

        return $this->render('no_found');


    }

    public function actionOnlineOrden($token='')
    {
        $this->layout = false;

        if( strlen( $token) > 50 ){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            $orden= OrdenBussines::findOne(['token'=> $token]);
            if( !is_null( $orden )) {
                if ($orden->pagado){
                    if( !$orden->firmado_digitalmente){
                        return $orden->pdf();
                    }else{
                        $ruta_archivo=__DIR__ . "/../../../media/ordenes/" .$orden->codigo.'.pdf';
                        if (file_exists($ruta_archivo)) {
                            return Yii::$app->response->sendFile($ruta_archivo,$orden->codigo.'.pdf' , ['inline' => true])->send();
                        } else {
                            return $orden->pdf();
                        }
                    }
                }else{
                    $pdf = new PDF_ORDEN_NO_PAGADA($orden);
                    $pdf->outputPDF(); // Envía PDF al navegador

                }

            }
        }

        return $this->render('no_found');


    }
    
   
    
  
    


}
