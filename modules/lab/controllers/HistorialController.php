<?php

namespace app\modules\lab\controllers;

use app\modules\lab\models\Examen;
use app\modules\lab\grids\HistoriaGrid;
use app\modules\lab\pdfs\PDF_HISTORIAL;
use app\modules\site\bussines\UserBussines;
use app\modules\site\models\User;
use Yii;
use yii\db\Expression;
use yii\web\Controller;
use yii\web\Response;


/**
 * UserController implements the CRUD actions for User model.
 */
class HistorialController extends Controller
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
                        'actions' => ['index','clientes','analisis','imprimir'],
                        'allow' => true,
                        'roles' => ['operador','administrador'],
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
        $model = new HistoriaGrid();
       

        return $this->render('index', [ 'model' => $model]);
    }
    
    public function actionClientes(){
        $this->layout = FALSE;
        $keyword = strval($_POST['query']);
        
        $clientes =UserBussines::find()->alias("u")
        ->select(['u.id', 'username','email' ,'identificacion', 'nombres', 'edad', 'sexo_id', 'telefono','direccion','unidad_tiempo',
            'activo','concat( identificacion,\' \',nombres,\' \' ,email) as _descripcion'])
            ->innerJoin('auth_assignment','u.id=auth_assignment.user_id')
            ->where(['item_name'=>'cliente'])
            ->andWhere(['like', new Expression('concat( identificacion,\' \',nombres,\' \' ,email)'),"$keyword"])->all();
            
            $response=[];
            foreach ($clientes as $cliente) {
                $attribute = $cliente->attributes;
                $attribute['_descripcion']=$cliente->_descripcion;
                $response[] = $attribute;
            }
            return json_encode($response);
    } 
    
    public function actionAnalisis()
    {
      
           
        $this->layout = null;
            Yii::$app->response->format = Response::FORMAT_JSON;
            $user = new User();
            if( Yii::$app->request->post('User')['id'] > 0){
                $user= User::find()->where(['id'=> Yii::$app->request->post('User')['id'] ])->one();
                if( !is_null($user)){
                    $connection = Yii::$app->db;
                    
                    $analisis = $connection->createCommand('SELECT orden.paciente_id, analisis.id , analisis.nombre, count( analisis.id ) as numero FROM analisis 
                                inner join examen on examen.analisis_id = analisis.id 
                                inner join orden on orden.id = examen.orden_id
                                where orden.paciente_id = :cliente_id
                                group by  orden.paciente_id, analisis.id, analisis.nombre
                                order by analisis.nombre asc
                                ',[':cliente_id'=>$user->id])->queryAll();
                   return $this->render('_analisis', ['analisis'=>$analisis]);
                }
            }
            
            
            
           
            
       
    }
    
    public function actionImprimir($cliente,$analisis){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;       
        $cliente = UserBussines::findOne(['id'=>$cliente]);
        if( is_null( $cliente)){ return 'El cliente no existe !!';}
        $analisis = Examen::find()
        ->joinWith(['orden'])
        ->where(['orden.paciente_id'=>$cliente->id])
        ->andWhere(['analisis_id' => $analisis])
        ->all();
       $pdf = new PDF_HISTORIAL($cliente, $analisis);
       return ($pdf->Output(''));
        
    }
    


}
