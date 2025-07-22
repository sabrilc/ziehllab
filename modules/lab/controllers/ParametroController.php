<?php

namespace app\modules\lab\controllers;

use Yii;
use app\modules\lab\models\Analisis;
use app\modules\lab\models\Parametro;
use app\modules\lab\grids\ParametroGrid;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\lab\models\Registro;

/**
 * ParametroController implements the CRUD actions for Parametro model.
 */
class ParametroController extends Controller
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
                        'roles' => ['operador','administrador'],
                    ],
                ],
            ],
        ];
    }


    /**
     * Displays a single Parametro model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model= $this->findModel($id);
        return $this->render('view', [
            'model' =>$model,
            'analisis'=>Analisis::findOne($model->analisis_id),
        ]);
    }

    /**
     * Creates a new Parametro model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($analisis)
    {
        $model = new Parametro();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Registro::onCreated($model);
            return $this->redirect(['view', 'id' => $model->id]);
        }
        $model->analisis_id=$analisis;
        return $this->render('create', [
            'model' => $model,
            'analisis'=>Analisis::findOne($analisis),
        ]);
    }

    /**
     * Updates an existing Parametro model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Registro::onUpdated($model);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'analisis'=>Analisis::findOne($model->analisis_id),
        ]);
    }

    /**
     * Deletes an existing Parametro model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model=$this->findModel($id);
        $analisis=$model->analisis_id;
        $model->delete();

        return $this->redirect(['/analisis/update','id'=>$analisis]);
    }

    /**
     * Finds the Parametro model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Parametro the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Parametro::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
