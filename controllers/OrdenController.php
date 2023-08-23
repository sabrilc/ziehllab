<?php

namespace app\controllers;

use app\models\Analisis;
use app\models\Antibiotico;
use app\models\Examen;
use app\models\ExamenGermen;
use app\models\ExamenGermenAntibiotico;
use app\models\ExamenParametro;
use app\models\Orden;
use app\models\OrdenSearch;
use app\models\PDF_INFORME;
use app\models\Registro;
use app\models\User;
use Yii;
use yii\db\Exception;
use yii\db\Expression;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * OrdenController implements the CRUD actions for Orden model.
 */
class OrdenController extends Controller
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
                        'actions' => ['index','index-con-analisis','nueva','create','update','view','delete','clientes','add-cliente',
                                      'borra-agente-cultivo','ingreso-resultado',
                                      'guardar-resultados','imprimir-resultado','ver-resultado',
                                      'finalizar','guardar-prueba-sensiblidad','guardar-germen','borrar-germen',
                                      'examen-plantilla','examenes','prueba-sensibilidad-examen-germen',
                                      'imprimir','pagar','descuento','poner-en-proceso','enviar-mail','guardar-info'
                            
                         ],
                        'allow' => true,
                        'roles' => ['operador'],
                    ],
                    [
                        'actions' => ['informe','descarga'],
                        'allow' => true,
                        'roles' => ['administrador'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    
    
    public function actionClientes(){
        $this->layout = FALSE;
        $keyword = strval($_POST['query']);
        
        $clientes =User::find()
        ->select(['user.id', 'username','email' ,'identificacion', 'nombres', 'edad', 'sexo_id', 
            'telefono','direccion','unidad_tiempo','email_notificacion',
            'activo','concat( identificacion,\' \',nombres,\' \' ,email) as _descripcion'])
         ->innerJoin('auth_assignment','user.id=auth_assignment.user_id')
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
    
    public function actionAddCliente(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $user = new User();
      //  $user->setScenario('nuevo');
      //  $user->dbremove=false;
        if( Yii::$app->request->post('User')['id'] > 0){
            $user= User::find()->where(['id'=> Yii::$app->request->post('User')['id'] ])->one();
            if( is_null($user)){
                $user = new User();
                $user->setScenario('nuevo');
            }
        }
        
        
        
        if($user->load( Yii::$app->request->post() ) && $user->save() ){
                       
        }
        
        if( !empty($user->errors)){
            return array('errors'=>$user->errors);
        }
        
        return array('success'=>true);
        
        
        
        
    }
    
    
    public function actionPonerEnProceso($id){
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $orden = Orden::find()->where(['id'=>$id])->one();
            $orden->cerrada = false;
            $orden->save(false);
            $transaction->commit();
            $session = \Yii::$app->session;
            $session->setFlash('success',"Orden #". str_pad( $orden->id,10, "0", STR_PAD_LEFT)." puesta en proceso");
            $this->redirect(['index']);
        } catch (\Exception $e) {
            throw new HttpException('500',"Error al cambiar el estado de la orden");
            $transaction->rollBack();
        }        
    }
    
    public function actionEnviarMail($id){
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $orden = Orden::find()->where(['id'=>$id])->one();
            $errors = $orden->enviarMail();         
            $transaction->commit();
            $session = \Yii::$app->session;
            if(array_key_exists ('success',$errors)){
                $session->setFlash('success',"Se ha enviado los resultados al correo: ". $orden->paciente->email_notificacion );
            }
            
            if(array_key_exists ('warning', $errors)){
                $session->setFlash('warning', 'no se ha podido enviar el mensaje porque el correo del cliente es invalido..!' );
            }          
           
        } catch (\Exception $e) {
            throw new HttpException('500',$e->getMessage());
            $transaction->rollBack();
        }
        
        $this->redirect(['index']);
    }

    public function actionInforme(){
      \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW; 

        if( !empty(  Yii::$app->request->post()['fecha_inicio'] ) && !empty( Yii::$app->request->post()['fecha_fin'] ) ){
            echo header("Content-type: application/pdf");
            echo header('Content-Disposition: attachment; filename=Informe.pdf');
            $pdf = new PDF_INFORME(  Yii::$app->request->post()['fecha_inicio'], Yii::$app->request->post()['fecha_fin'] );
			//return  $pdf->Output();
            return  $pdf->Output('','S');
        }
    
       return $this->render('informe',[ ]);     
    
    }
    
    public function actionDescarga(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        
        if( !empty(  Yii::$app->request->post()['fecha_inicio'] ) && !empty( Yii::$app->request->post()['fecha_fin'] ) ){
           // echo header("Content-type: application/pdf");
           // echo header('Content-Disposition: attachment; filename=Informe.pdf');
          
            $file = \Yii::createObject([
                'class' => 'codemix\excelexport\ExcelFile',
                
                'writerClass' => '\PHPExcel_Writer_Excel2007', // Override default of `\PHPExcel_Writer_Excel2007`
                
                'sheets' => [
                    
                    'ORDENES' => [
                        'class' => 'codemix\excelexport\ActiveExcelSheet',
                        'query' => Orden::find()->where(['between', 'fecha', Yii::$app->request->post()['fecha_inicio'],  Yii::$app->request->post()['fecha_fin'] ]),
                        
                        // If not specified, all attributes from `User::attributes()` are used
                        'attributes' => [
                            'codigo',
                            'fecha',
                            'paciente.nombres',
                            'doctor.nombres',
                            'precio',
                            'descuento',
                            'valor_total'
                            
                        ],
                        
                        // If not specified, the label from the respective record is used.
                        // You can also override single titles, like here for the above `team.name`
                        'titles' => [
                            'C' => 'PACIENTE',
                            'D' => 'DOCTOR',
                            'D' => 'SUBTOTAL',
                        ],
                    ],
                    'ANALISIS' => [
                        'class' => 'codemix\excelexport\ActiveExcelSheet',
                        'query' => Examen::find()
                                    ->joinWith('orden','analisis')
                                    ->where(['between', 'fecha', Yii::$app->request->post()['fecha_inicio'],  Yii::$app->request->post()['fecha_fin'] ]),
                        
                        // If not specified, all attributes from `User::attributes()` are used
                        'attributes' => [
                            'orden.codigo',
                            'orden.fecha',
                            'orden.paciente.nombres',
                            'orden.doctor.nombres',
                            'orden.precio',
                            'orden.descuento',
                            'orden.valor_total',
                            'analisis.nombre',
                            'analisis.precio'
                            
                        ],
                        
                        // If not specified, the label from the respective record is used.
                        // You can also override single titles, like here for the above `team.name`
                        'titles' => [
                            'C' => 'PACIENTE',
                            'D' => 'DOCTOR',
                            'E' => 'SUBTOTAL',
                            'E' => 'SUBTOTAL',
                            'H' => 'ANALISIS',
                            'I' => 'VALOR'
                        ],
                    ],
                    
                ],
            ]);
            $file->send('REPORTE('.Yii::$app->request->post()['fecha_inicio'].')('.Yii::$app->request->post()['fecha_fin'].').xlsx');
            
        }
        
        return $this->render('descarga',[ ]);
        
    }

    // ajax 
     public function actionFinalizar($orden_id){
         \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
         if(isset( $orden_id )){
             $orden=Orden::findOne($orden_id);
              return  $orden->finalizar();
            }  
     }
     
     public function actionPagar($orden_id){
         if(isset( $orden_id )){
             $orden=Orden::findOne($orden_id);
             return  $orden->pagar();
         }
     }
     
     public function actionDescuento($orden_id){
         if(isset( $orden_id )){
             $orden=Orden::findOne($orden_id);            
             
             return  $orden->descuento();
         }
     }
    
    //ajax    
     public function actionGuardarPruebaSensiblidad(){    
     
     $examen_germen_id =Yii::$app->request->post()['examen-germen-id'];
     $valor=Yii::$app->request->post()['valor'];
     $tipo=Yii::$app->request->post()['tipo'];
     $antibiotico=Yii::$app->request->post()['antibiotico'];

     foreach ( $examen_germen_id as $index => $germen) {
         if($valor[ $index ] != '' && $tipo[ $index ] !='' ) {
                    $examenGermenAntibiotico =  ExamenGermenAntibiotico::find()->where(
                     [ 'examen_germen_id' => $examen_germen_id[$index],
                         'antibiotico_id' => $antibiotico[$index] ])->One();                 
                 
          
                     if(!isset($examenGermenAntibiotico)){
                         $examenGermenAntibiotico=new ExamenGermenAntibiotico();        
                     }
                    
                     $examenGermenAntibiotico->examen_germen_id = $germen;
                     $examenGermenAntibiotico->antibiotico_id= $antibiotico[$index];
                     $examenGermenAntibiotico->valor = $valor[ $index ];
                     $examenGermenAntibiotico->tipo = $tipo[ $index ];
                     $examenGermenAntibiotico->save(false);   
                     }
     
            }
     }
    
    //ajax
    public function actionGuardarGermen(){
        $mensaje='Transaccion Fallida..!';      
        $examenGermen = new ExamenGermen();        
        if ($examenGermen->load(Yii::$app->request->post())) {  
            $mensaje='El Germen ya ha sido ingresado en el Cultivo..!';
            $germenEnCultivo= ExamenGermen::find()->where(['examen_id'=>$examenGermen->examen_id,'germen_id'=>$examenGermen->germen_id])->one();
            if($germenEnCultivo==null){
                $examenGermen->save();                
                $mensaje='Transacion Exitosa..!';
            }            
        }      
       
        return $mensaje;
        
    }
    
    
    //ajax
    public function actionGuardarInfo(){
        if(isset(Yii::$app->request->post('Orden')['_id'])){
            $model = Orden::findOne( Yii::$app->request->post('Orden')['_id']);         
            if( $model->load( Yii::$app->request->post() )  ){
                if( $model->fecha_resultados ==''){ $model->fecha_resultados = new Expression(" current_date"); }
                if( $model->hora_resultados ==''){ $model->hora_resultados = new Expression(" current_time"); }
                if($model->save(false)){
                return 'OK';
                }
            }
        }
        return "FAIL";
    }
    
    //ajax    
    public function actionGuardarResultados(){
        if(isset(Yii::$app->request->post()['parametro_id'])){
        $parametros=Yii::$app->request->post()['parametro_id'];
        $referencias=Yii::$app->request->post()['referencia'];
        $medidas=Yii::$app->request->post()['medida'];
        $valores=Yii::$app->request->post()['valor'];
        $i=0;
        $id='';
        foreach ( Yii::$app->request->post()['examen_id'] as $examen) {
            $examenParametro=ExamenParametro::findOne(['examen_id'=>$examen,'parametro_id'=>$parametros[$i]]);
            if(!isset($examenParametro)){
                $examenParametro=new ExamenParametro();
                $examenParametro->examen_id=$examen;
                $examenParametro->parametro_id=$parametros[$i];
                
                
            }
            $examenParametro->medida=$medidas[$i];
            $examenParametro->valor=$valores[$i];
            $examenParametro->referencia=$referencias[$i];
            $examenParametro->descripcion=$examenParametro->parametro->descripcion;
            $examenParametro->save(false);
            $i++;
            $id = $examen ;
        }
        
        $examen = Examen::findOne($id);
        $examen->nota= trim ( Yii::$app->request->post()['Examen']['nota'] );
        $examen->save(false);
        return $examen->getForm();
        }
        return "El analisis que intentas ingresar resultado no tiene esquema ingresado";
    }
    
    
    //ajax
    public function actionExamenPlantilla($examen_id){
        $examen = Examen::findOne($examen_id);              
        return $examen->getForm();
        
    }
    
    //ajax
    public function actionExamenes($orden_id){
        
        $form = ActiveForm::begin();
        $orden = Orden::findOne($orden_id);
        $orden->_id =$orden->id;
        $html="<div class='row mt-3 animated fadeIn'>
                  <div class='col-xs-12 col-sm-12'>
                    <div class='panel panel-primary'>
                        <div class='panel-heading'>Orden</div>
                          <div class='panel-body'
                              <div class='row'>
                              <form class='animated fadeIn' id='formularioOrden' onsubmit='return guardarInfoOrden( )' >";
                             $html.= Html::csrfMetaTags();
                             $html.= Html::activeHiddenInput($orden,'_id');
                             $html.='<div class="col-md-6">'. $form->field($orden,'paciente_info')->textarea(['rows' => '3', 'onBlur'=>'guardarInfoOrden()', 'maxlength' => true,'placeholder'=> 'Se muestra unicamente en el formato AccessLab']).'</div>';
                             $html.= '<div class="col-md-6">'.$form->field($orden,'solicitante_info')->textarea(['rows' => '3', 'onBlur'=>'guardarInfoOrden()', 'maxlength' => true,'placeholder'=> 'Se muestra unicamente en el formato AccessLab']).'</div>';
                             $html.= '<div class="col-md-12">' 
                                 .$form->field($orden,'fecha_resultados')->textInput(['onChange'=>'guardarInfoOrden()'])
                                 .$form->field($orden,'hora_resultados')->textInput().'</div>';
                  $html.= "</form>  
                           </div>
                          
                         </div>
                        </div>
                    </div>     
                </div>
            </div>";
        
        $html.="<div class='row mt-3 animated fadeIn'>
                  <div class='col-xs-6 col-sm-3 sidebar-offcanvas'>
                <div class='panel panel-primary'>
                    <div class='panel-heading'>Análisis</div>                 
                        <div class='list-group'>";                                          
                                foreach ($orden->examens as $item) {
                                  $html.="<a  class='list-group-item'  onClick=cargarPlantillaExamen($item->id)>". $item->analisis->nombre ."</a>";            
                                }
                 $html.="</div>     
                        <div class='btn-group' role='group' aria-label='Opciones'>
                          <button type='button' class='btn btn-primary'  onclick='finalizarOrden($orden_id)'>Finalizar Orden </button>
                         <button type='button' class='btn btn-default'  onclick='imprimirOrden($orden_id)'>Imprimir Orden</button>
                        </div>                   
                     
                     </div> 
                    </div>  
              

                 <div class='col-xs-12 col-sm-9'>
                
                    <div class='panel panel-default'>
                        <div class='panel-heading'>Plantilla</div>
                         <div class='panel-body'  id='area_de_trabajo'> </div>                 
                    
                    </div>
                 </div>
               </div>";
                 $html.=" <script>
  $( function() {
    $( '#orden-fecha_resultados' ).datepicker($.extend({}, $.datepicker.regional['es'], { \"dateFormat\":\"yy-mm-dd\"}));   
    $('#orden-hora_resultados').clockTimePicker( {
         onChange: function(newVal, oldVal) { guardarInfoOrden(); },
      
   });

 $('#orden-hora_resultados').clockTimePicker('value', '". date('H:i', strtotime( '2000-01-01 ' . $orden->hora_resultados)). "');   

  } );
 
  </script>";
        return $html;
        
    } 
    
    
    //ajax
    public function actionPruebaSensibilidadExamenGermen($examen_germen_id=0){
        $html = "<form class='animated fadeIn' id='formularioSensiblidad$examen_germen_id' onsubmit='return guardarPruebaSensiblidad($examen_germen_id)'>";
        $html .= "<table class='table'>
                  <thead>
                    <tr>
                      <th scope='col'>#</th>
                      <th scope='col'>Antibiotico</th>
                      <th scope='col'>INHIBICION EN [MM]</th>
                      <th scope='col'>DIAMETRO DE ZONA</th>
                    </tr>
                  </thead>
                  <tbody>";
     
        if(isset($examen_germen_id)){
         
          foreach ( Antibiotico::find()->orderBy(['descripcion'=> SORT_ASC])->all() as $index => $antibiotico) {           
            $examenGermenAntibiotico = ExamenGermenAntibiotico::find()->where(
                                      [ 'examen_germen_id' => $examen_germen_id,
                                        'antibiotico_id' => $antibiotico->id ])->One();
          
           
           if(isset($examenGermenAntibiotico)){
               $html .="<tr>
                   <th scope='row'>". ($index+1) ." </th>
                   <td>$antibiotico->descripcion</td>
                   <td> <input name='examen-germen-id[]' value='$examen_germen_id' type='hidden' class='form-control'>
                        <input name='antibiotico[]' value='$antibiotico->id' type='hidden' class='form-control'>
                        <input type='text' name='valor[]' value='".$examenGermenAntibiotico->valor."' class='form-control'> </td>
                   <td>
                          <select name='tipo[]' class='form-control' >
                          <option value=''> Seleccionar ...</option>";
                       if($examenGermenAntibiotico->tipo == 'RESISTENTE'){
                           
                           $html.="<option value='RESISTENTE' selected>RESISTENTE</option>
                                  <option value='SENSIBLE'>SENSIBLE</option>";
                       }
                       else{
                           $html.="<option value='RESISTENTE'>RESISTENTE</option>
                                  <option value='SENSIBLE' selected> SENSIBLE</option>";
                       }
                       $html .="</select>
                        </td>
                   </tr>";
                                    
          
           }
           else{
               $html .="<tr>
                   <th scope='row'>". ($index+1) ." </th>
                   <td>$antibiotico->descripcion</td>
                   <td><input name='examen-germen-id[]'   value='$examen_germen_id'  type='hidden' class='form-control'>
                   <input name='antibiotico[]' value='$antibiotico->id' type='hidden' class='form-control'>
                        <input type='text' name='valor[]'class='form-control'> </td>
                   <td>
                          <select name='tipo[]' class='form-control'>
                          <option value=''> Seleccionar ...</option>
                          <option value='RESISTENTE'>RESISTENTE</option>
                          <option value='SENSIBLE'>SENSIBLE</option>
                        </select>
                        </td>
                   </tr>";
           }
         
        
        }
        }
        $html.="  </tbody>
                </table>
              <button class='btn btn-success'>Guardar Prueba de Sensiblidad </button>
             </form>";
        
        return $html;
    }    
    
    //ajax
    public function actionBorrarGermen(){
        $examenGermen = new ExamenGermen();
        if ($examenGermen->load(Yii::$app->request->post())) {            
            $examenGermen = ExamenGermen::findOne(['id'=>$examenGermen->_id]); 
            if(isset( $examenGermen )){
            foreach ($examenGermen->examenGermenAntibioticos as $examenGermenAntibiotico) {
                $examenGermenAntibiotico->delete();
            }
         $examenGermen->delete();
            }
        }
        
    }
    
    public function actionIngresoResultado(){
        $examen= new Examen();        
        return $this->render('ingreso-resultado',['examen'=>$examen]);
        
    }

  
/**
 * Visuliza los resultado de una orden
 * @param  $id 
 * @return string
 */  
    public function actionVerResultado($id){
        $orden= Orden::findOne($id);                
        return $this->render('ver-resultado',['orden'=>$orden]);        
    }
    
    
    
    public function actionImprimir($id){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $orden= Orden::findOne($id);
        return $orden->pdf();
        
    }
       
    
    /**
     * Lists all Orden models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrdenSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * Lists all Orden models.
     * @return mixed
     */
    public function actionIndexConAnalisis()
    {
        $searchModel = new OrdenSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index_con_analisis', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Orden model.
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
     * Creates a new Orden model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        
        $model = Orden::nueva();
        if(isset($model->id)){                
                return $this->redirect(['view', 'id' => $model->id]);
                
            }
        return $this->render('create', [
            'model' => $model,
        ]);
        
       }

       public function actionNueva()
       {
           
           $model = Orden::nueva();
           if(isset($model->id)){                
                   return $this->redirect(['view', 'id' => $model->id]);
                   
               }
           return $this->render('nueva', [
               'model' => $model,
           ]);
           
          }

    /**
     * Updates an existing Orden model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if(!isset ($model->descuento) ){ $model->descuento=0; };
        if ($model->load(Yii::$app->request->post())) {
            
            $transaction = Yii::$app->db->beginTransaction();
            try {
               
                if(isset(Yii::$app->request->post()['analisis'])){
                    $examenesSeleccionados=  Yii::$app->request->post()['analisis'];
                    foreach ($examenesSeleccionados as $examen) {
                        
                        $analisis = Analisis::findOne($examen);
                        $ordenExamenes = Examen::findOne(['orden_id'=>$model->id,'analisis_id'=>$analisis->id]);
                        
                        if( !isset( $ordenExamenes ) ){
                            $ordenExamenes= new Examen();
                            $ordenExamenes->analisis_id=$analisis->id;
                            $ordenExamenes->precio=$analisis->precio;
                            $ordenExamenes->orden_id=$model->id;
                            $ordenExamenes->save(false);
                        }
                        
                        
                        
                    }
                    
                    //  Elimina los Examenes que dejaron de seleccionarse
                    foreach ( Examen::findAll(['orden_id'=>$model->id]) as $examenInDB) {
                        $debeSerBorrado=true;
                        foreach ($examenesSeleccionados as $examen) {
                            if( $examenInDB->analisis_id == $examen){
                                $debeSerBorrado=false;
                            }
                        }
                        
                        if($debeSerBorrado){
                            foreach ($examenInDB->examenParametros as $examenParametrosInDB) {
                                $examenParametrosInDB->delete();
                            }
                            $examenInDB->delete(); }
                            else{
                                //actualizarPrecio
                                $examenInDB->precio =$examenInDB->analisis->precio;
                                $examenInDB->save(false);
                            }
                            
                    }
                    
                    $model->precio = Examen::find()->where(['orden_id'=>$model->id])->sum('precio');
                    
                    $model->valor_total = $model->precio;
                    Registro::onUpdated($model);
                    
                    
                }
                
                
                $transaction->commit();
            }catch (Exception $e){
                $transaction->rollBack();
                return $this->render('update', [
                    'model' => $model,
                ]);
                
            }
                      
            
            return $this->redirect(['view', 'id' => $model->id]);
        
        }
    
        return $this->render('update', [
            'model' => $model,
        ]);

        
    }

    /**
     * Deletes an existing Orden model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model=  $this->findModel($id);
        $examenes= $model->examens;
        foreach ($examenes as $examen) {
           $parametrosExamen= $examen->examenParametros;
           foreach ($parametrosExamen as $examenParametro) {  
             
               $examenParametro->delete();
               
           }
           $examen->delete();
        }
        $model->delete();
    

        return $this->redirect(['index']);
    }

    /**
     * Finds the Orden model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Orden the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Orden::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
