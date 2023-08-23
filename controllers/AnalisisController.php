<?php

namespace app\controllers;

use app\models\Analisis;
use app\models\AnalisisSearch;
use app\models\ParametroSearch;
use app\models\Registro;
use app\models\SeccionSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * AnalisisController implements the CRUD actions for Analisis model.
 */
class AnalisisController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index','create','update','view','delete'],
                        'allow' => true,
                        'roles' => ['operador','administrador'],
                    ],
                ],
            ],
        ];
    }
    

    /**
     * Lists all Analisis models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AnalisisSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Analisis model.
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
     * Creates a new Analisis model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Analisis();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->codigo=str_pad($model->id, 10, "0", STR_PAD_LEFT); 
            Registro::onCreated($model);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Analisis model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
          //  $model->codigo=str_pad($model->id, 10, "0", STR_PAD_LEFT);
            Registro::onUpdated($model);
            return $this->redirect(['view', 'id' => $model->id]);
        }

            
        $searchModel = new ParametroSearch();
        $values=Yii::$app->request->queryParams;      
        $dataProvider = $searchModel->search($values,$model->id);
        
        
        $searchSeccion = new SeccionSearch();
        $dataProviderSeccion = $searchSeccion->search($values,$model->id);
        
        return $this->render('update', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'searchSeccion' => $searchSeccion,
            'dataProviderSeccion' => $dataProviderSeccion,
        ]);
    }

    /**
     * Deletes an existing Analisis model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Analisis model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Analisis the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Analisis::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
