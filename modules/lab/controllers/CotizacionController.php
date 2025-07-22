<?php

namespace app\modules\lab\controllers;

use app\modules\lab\grids\CotizacionGrid;
use app\modules\lab\models\Analisis;
use app\modules\lab\models\Cotizacion;
use app\modules\lab\models\CotizacionAnalisis;
use app\modules\lab\models\Registro;
use Yii;

use yii\db\Expression;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CotizacionController implements the CRUD actions for Cotizacion model.
 */
class CotizacionController extends Controller
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
                            'actions' => ['index','create','update','view','delete'
                                
                            ],
                            'allow' => true,
                            'roles' => ['operador'],
                        ],
                        
                        [
                            'actions' => ['nueva'],
                            'allow' => true,
                            'roles' => ['?','@'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ];
        }

    /**
     * Lists all Cotizacion models.
     * @return mixed
     */
    public function actionIndex()
    {
        $GridModel = new CotizacionGrid();
        $dataProvider = $GridModel->Grid(Yii::$app->request->queryParams);

        return $this->render('index', [
            'GridModel' => $GridModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * Lists all Cotizacion models.
     * @return mixed
     */
    public function actionNueva()
    {
        
    
        $mensaje='';
        $model= new Cotizacion();
        if(isset(Yii::$app->request->post()['analisis'])){
        /*
        if ($model->load(Yii::$app->request->post()) && $model->save()) {        
            
          $examenesSeleccionados=  Yii::$app->request->post()['analisis'];
          $textAnalisis='<ul>';
          foreach ($examenesSeleccionados as $examen) {
             
              $analisis=Analisis::findOne($examen);
              $cotizacionAnalisis= new CotizacionAnalisis();
              $cotizacionAnalisis->analisis_id=$analisis->id;
              $cotizacionAnalisis->precio=$analisis->precio;
              $cotizacionAnalisis->cotizacion_id=$model->id; 
              $cotizacionAnalisis->save(false);
              $textAnalisis.='<li>'.$analisis->nombre.'</li>';          

              
              
          }
          $textAnalisis.='</ul>';
          $model->total=CotizacionAnalisis::find()->where(['cotizacion_id'=>$model->id])->sum('precio');
          $model->fecha= new Expression('now()');
          $model->created_at= new Expression('now()');
          $model->codigo=str_pad($model->id, 10, "0", STR_PAD_LEFT);
          $model->vigente=true;
          $model->vista=true;
          $model->save();
          
          
          Yii::$app->mailer->compose()
          ->setFrom('notificacion@ziehllab.com')
          ->setTo($model->email)
          ->setSubject('COTIZACION DE EXAMENES CLINICOS')
          ->setHtmlBody("Saludos, estimado(a) usuario la cotizaci�n de las examenes cl�nicos:
                                    ".$textAnalisis."tiene un valor total de $ $model->total ; <br>
                                    Con mucho gusto de realizar sus an�lisis les esperamos en nuestro laboratorio.<br><br>
              Direccion: Av 10 de Agosto entre 9 de noviembre y Jaime Rold�s, frente a Disensa.<br>
              Telefono: (052) 020353")
          ->send();
         
          
          
          $mensaje="SU COTIZACION HA SIDO ENVIADA AL CORREO: ".$model->email;
        }*/
       
        }
        $model= new Cotizacion();
        return $this->render('nueva',['model'=>$model,'mensaje'=>$mensaje]);
    }

    /**
     * Displays a single Cotizacion model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Cotizacion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
  
        $model= new Cotizacion();
        if(isset(Yii::$app->request->post()['analisis'])){
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                
                $examenesSeleccionados=  Yii::$app->request->post()['analisis'];
                foreach ($examenesSeleccionados as $examen) {
                    $analisis=Analisis::findOne($examen);
                    $cotizacionAnalisis= new CotizacionAnalisis();
                    $cotizacionAnalisis->analisis_id=$analisis->id;
                    $cotizacionAnalisis->precio=$analisis->precio;
                    $cotizacionAnalisis->cotizacion_id=$model->id;
                    $cotizacionAnalisis->save(false);
                    
                  
                }
                
                $model->total=CotizacionAnalisis::find()->where(['cotizacion_id'=>$model->id])->sum('precio');
                $model->fecha= new Expression('now()');
                $model->created_at= new Expression('now()');
                $model->codigo=str_pad($model->id, 10, "0", STR_PAD_LEFT); 
                $model->vigente=true;
                $model->vista=true;
                $model->save(false);
                return $this->redirect( ['view', 'id' => $model->id]);
            }}

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Cotizacion model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
    
        $model = $this->findModel($id);        
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if(isset(Yii::$app->request->post()['analisis'])){
                $examenesSeleccionados=  Yii::$app->request->post()['analisis'];
                foreach ($examenesSeleccionados as $examen) {
                    
                    $analisis=Analisis::findOne($examen);
                    
                    $ordenExamenes=CotizacionAnalisis::findOne(['cotizacion_id'=>$model->id,'analisis_id'=>$analisis->id]);
                    
                    if(!isset($ordenExamenes)){
                        $ordenExamenes= new CotizacionAnalisis();
                        $ordenExamenes->analisis_id=$analisis->id;
                        $ordenExamenes->precio=$analisis->precio;
                        $ordenExamenes->cotizacion_id=$model->id;
                        $ordenExamenes->save(false);
                    }
                    
                    
                    
                }
                
                //  Elimina los Examenes que dejaron de seleccionarse
                foreach (CotizacionAnalisis::findAll(['cotizacion_id'=>$model->id]) as $examenInDB) {
                    $debeSerBorrado=true;
                    foreach ($examenesSeleccionados as $examen) {
                        if($examenInDB->analisis_id==$examen){
                            $debeSerBorrado=false;
                        }
                    }
                    
                    if($debeSerBorrado){ $examenInDB->delete(); }
                    
                }
                
                $model->total=CotizacionAnalisis::find()->where(['cotizacion_id'=>$model->id])->sum('precio');
                Registro::onUpdated($model);
                
                
            }
            
            return $this->redirect(['view', 'id' => $model->id]);
        }
        
        return $this->render('update', [
            'model' => $model,
        ]);
        
        
        
    }

    /**
     * Deletes an existing Cotizacion model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $cotizacion=$this->findModel($id);
        
        foreach ($cotizacion->cotizacionAnalises as $cotizacionAnalisis) {
            $cotizacionAnalisis->delete();
        }
        
        $cotizacion->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Cotizacion model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Cotizacion the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Cotizacion::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
