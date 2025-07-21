<?php

namespace app\modules\lab\controllers;

use app\models\AdminGrid;
use app\models\AuthAssignment;
use app\models\Registro;
use app\models\User;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\db\Query;


/**
 * UserController implements the CRUD actions for User model.
 */
class AdminController extends Controller
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
                        'actions' => ['index','create','update','view','delete','reporte'],
                        'allow' => true,
                        'roles' => ['administrador'],
                    ],
                ],
            ],
        ];
    }

    
    public function actionReporte(){
        $file = \Yii::createObject([
            'class' => 'codemix\excelexport\ExcelFile',
            'sheets' => [
                
                'Users' => [
                    'data' =>  ( new \yii\db\Query() )
                    ->select(['precio','valor_total'])
                    ->from('orden')
                    ->where(['between', 'fecha', '2019-05-02','2019-09-19'])
                    ->sum('precio'),
                    'titles' => [ 'Name', 'Email'],
                    ],
                    ]
                    ]);
        
        $file->send('demo.xlsx');
    }
    
/**
 *  lista todo los usuarios
 */
    public function actionIndex()
    {
        $GridModel = new AdminGrid();
        $dataProvider = $GridModel->Grid(Yii::$app->request->queryParams);

        return $this->render('index', [
            'GridModel' => $GridModel,
            'dataProvider' => $dataProvider,
        ]);
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
       if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $addrol= new AuthAssignment();
            $addrol->item_name='administrador';
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
