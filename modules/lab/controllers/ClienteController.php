<?php

namespace app\modules\lab\controllers;

use app\models\AuthAssignment;
use app\models\ClienteGrid;
use app\models\Orden;
use app\models\Registro;
use app\models\User;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\ClienteResultadoGrid;
use common\Tools;


/**
 * UserController implements the CRUD actions for User model.
 */
class ClienteController extends Controller
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
                        'actions' => ['index','create','update','view','delete'],
                        'allow' => true,
                        'roles' => ['operador'],
                    ],
                    [
                        'actions' => ['resultados','imprimir'],
                        'allow' => true,
                        'roles' => ['cliente'],
                    ],
                ],
            ],
        ];
    }

/**
 *  lista todo los usuarios
 */
    public function actionIndex()
    {
        $GridModel = new ClienteGrid();
        $dataProvider = $GridModel->Grid(Yii::$app->request->queryParams);

        return $this->render('index', [
            'GridModel' => $GridModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionResultados()
    {
        $GridModel = new ClienteResultadoGrid();
        $dataProvider = $GridModel->Grid(Yii::$app->request->queryParams);
        return $this->render('resultados', [
            'GridModel' => $GridModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionImprimir($orden_id){   
	
       
		//$orden = \Yii::$app->getSecurity()->decryptByKey( base64_decode($orden_id), \Yii::$app->params['key_for_encode']);
		  $orden= base64_decode($orden_id);
		if($orden){
		 
        $orden= Orden::find()->where(['id' => $orden,
                                     'paciente_id'=>Yii::$app->user->identity->id,
                                     'pagado'=>true,
                                     'cerrada'=>true])->one();
		if( is_null($orden)){ return ""; }
        if( !$orden->firmado_digitalmente){
			 \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            return base64_encode($orden->pdf(false,true));
        }else{
           return  Tools::pdfToBase64(__DIR__ . "/../media/ordenes/" .$orden->codigo.'.pdf',$orden->codigo.'.pdf');
        }
		}
		 
		return "";
		
        
    }
    

/**
 * Visualiza los datos de un usuario
 * @param integer $id
 * @return string
 */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

  /**
   * Crear nuevos usuarios
   * @return \yii\web\Response|string
   */
    public function actionCreate()
    {
        $model = new User();
        $model->unidad_tiempo='AÑOS';
       if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $addrol= new AuthAssignment();
            $addrol->item_name='cliente';
            $addrol->user_id=$model->id;
            $addrol->save(false);
            unset($model->password);
            Registro::onCreated($model);
            
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

 /**
  * Actualiza los datos de un usuario
  */

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
     
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
          
            unset($model->password);
            Registro::onUpdated($model);
           
            return $this->redirect(['view', 'id' => $model->id]);
        }
        $model->password='';
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        
        $model=$this->findModel($id);
        $addrol=AuthAssignment::findOne(['user_id'=>$model->id]);
      
        $model->delete();
        $addrol->delete();
       

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
