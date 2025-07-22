<?php

namespace app\modules\lab\controllers;

use Yii;
use app\modules\lab\models\Laboratorista;
use app\modules\lab\grids\LaboratoristaGrid;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * LaboratoristaController implements the CRUD actions for Laboratorista model.
 */
class LaboratoristaController extends Controller
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
                        'roles' => ['administrador','operador'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Laboratorista models.
     * @return mixed
     */
    public function actionIndex()
    {
        $GridModel = new LaboratoristaGrid();
        $dataProvider = $GridModel->Grid(Yii::$app->request->queryParams);

        return $this->render('index', [
            'GridModel' => $GridModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Laboratorista model.
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
     * Creates a new Laboratorista model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Laboratorista();

        if ($model->load(Yii::$app->request->post())) {
            $model->dbremove = false;
            $model->save( false );
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Laboratorista model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        unset($model->firma_digital_secret);
        if ($model->load(Yii::$app->request->post())) {
            if( strlen($model->firma_digital_secret) < 3 ){
                unset($model->firma_digital_secret);
            }
            $model->save();
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            $model->p12File = UploadedFile::getInstance($model, 'p12File');
            $model->upload();
            if( empty($model->errors)) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Laboratorista model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
       $model= $this->findModel($id);
       $model->dbremove= true;
       $model->save(false);

        return $this->redirect(['index']);
    }

    /**
     * Finds the Laboratorista model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Laboratorista the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Laboratorista::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
