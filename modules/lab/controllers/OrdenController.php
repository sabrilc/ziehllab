<?php

namespace app\modules\lab\controllers;

use app\modules\lab\bussines\ExamenBussines;
use app\modules\lab\bussines\OrdenBussines;
use app\modules\lab\models\Analisis;
use app\modules\lab\models\Antibiotico;
use app\modules\lab\models\Registro;
use app\modules\lab\pdfs\PDF_ORDEN_TICKET;
use app\modules\site\bussines\UserBussines;
use app\modules\site\models\AuthAssignment;
use app\modules\lab\models\Examen;
use app\modules\lab\models\ExamenGermen;
use app\modules\lab\models\ExamenGermenAntibiotico;
use app\modules\lab\models\ExamenParametro;
use app\modules\lab\models\Orden;
use app\modules\lab\pdfs\PDF_INFORME;
use app\modules\site\models\User;
use app\modules\lab\grids\OrdenGrid;
use Yii;
use yii\db\Exception;
use yii\db\Expression;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;
use yii\web\Response;


class OrdenController extends Controller
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
                        'actions' => ['index','index-con-analisis','nueva','editar','paciente-list','view','delete','clientes','add-cliente',
                                      'borra-agente-cultivo','ingreso-resultado','online-ticket','info',
                                       'cliente-buscar','cliente-guardar','analisis-buscar','guardar','print-ticket','test',
                                      'guardar-resultados','imprimir-resultado','ver-resultado','online-orden',
                                      'finalizar','guardar-prueba-sensiblidad','guardar-germen','borrar-germen',
                                      'examen-plantilla','examenes','prueba-sensibilidad-examen-germen',
                                      'imprimir','imprimir-prueba','pagar','descuento','poner-en-proceso','enviar-mail',
                                      'guardar-info','guardar-responsables','firmar'
                            
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
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (in_array($action->id, ['cliente-guardar', 'guardar','print-ticket'], true)) {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
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
	
	public function actionOnlineTicket($id){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
		$orden = OrdenBussines::findOne($id);
        $ticket = new PDF_ORDEN_TICKET($orden);
		$ticket->outputPDF(); 
    }

    /******************** Funciones UI ReactJS *************************/

    public function actionEditar($id)
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
                    $model->firmado_digitalmente = false;
                    $model->fecha_firmado_digital = null;
                    $model->borrarDocumentoFirmado();
                    Registro::onUpdated($model);


                }


                $transaction->commit();
            }catch (Exception $e){
                $transaction->rollBack();
                return $this->render('editar', [
                    'model' => $model,
                ]);

            }


            return $this->redirect(['view', 'id' => $model->id]);

        }

        return $this->render('editar', [
            'model' => $model,
        ]);


    }


    public function actionInfo($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $orden = OrdenBussines::find()
            ->with(['paciente', 'examens']) // asegúrate que estén definidas las relaciones
            ->where(['id' => $id])
            ->one();

        if (!$orden) {
            return [
                'success' => false,
                'error' => 'Orden no encontrada',
            ];
        }

        // Transformar cliente
        $cliente = $orden->paciente ? [
            'id' => $orden->paciente->id,
            'nombres' => $orden->paciente->nombres,
            'identificacion' => $orden->paciente->identificacion,
            'email' => $orden->paciente->email,
            'email_notificacion' => $orden->paciente->email_notificacion,
            'telefono' => $orden->paciente->telefono,
            'direccion' => $orden->paciente->direccion,
            'fecha_nacimiento' => $orden->paciente->fecha_nacimiento,
            'sexo_id' => $orden->paciente->sexo_id,
        ] : null;

        // Transformar items
        $items = [];
        foreach ($orden->examens as $item) {
            $items[] = [
                'idProducto' => $item->analisis_id,
                'descripcion' => $item->analisis->nombre ?? '',
                'cantidad' => 1,
                'precio' => $item->precio,
            ];
        }

        return [
            'success' => true,
            'orden' => [
                'id' => $orden->id,
                'codigo_lab' => $orden->codigo_lab,
                'cliente' => $cliente,
                'items' => $items,
                'descuento' => $orden->porcentaje_desc,
            ]
        ];
    }

    public function actionGuardar()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $post = Yii::$app->request->post();

            // Validación defensiva del cliente
            if (!isset($post['cliente'])) {
                return $this->asJson(['success' => false, 'error' => 'Cliente no especificado']);
            }

            $clienteData = Json::decode($post['cliente']);
            if (!isset($clienteData['id'])) {
                return $this->asJson(['success' => false, 'error' => 'Seleccion un cliente']);
            }


            $orden =  OrdenBussines::findOne($post['orden_id']);
            $isnew=false;
            if(!$orden){
                $isnew = true;
                $orden = new OrdenBussines();
                $orden->codigo_secreto = OrdenBussines::getCodigoSecreto();

            }



            // Crear orden

            $orden->setScenario('registro');
            $orden->paciente_id = $clienteData['id'];

            if (!$orden->save()) {
                Yii::error('Error al guardar la orden: ' . json_encode($orden->getErrors()), __METHOD__);
                return $this->asJson(['success' => false, 'errors' => $orden->getErrors()]);
            }
            if($isnew){
                $orden->codigo = str_pad($orden->id, 10, '0', STR_PAD_LEFT);
                $orden->generateToken();
                $orden->save(false);
            }

            // Decodificar y validar items
            if (!isset($post['items'])) {
                return $this->asJson(['success' => false, 'error' => 'No se han recibido ítems']);
            }

            $items = Json::decode($post['items']);
            if (empty($items)) {
                return $this->asJson(['success' => false, 'error' => 'La orden esta vacia']);
            }

            // Procesar ítems y parámetros
            foreach ($items as $item) {
                $analisis = Analisis::findOne($item['idProducto']);
                if (!$analisis) {
                    throw new \Exception("Análisis con ID {$item['idProducto']} no encontrado.");
                }

                $ordenExamenes = Examen::findOne(['orden_id'=>$orden->id,'analisis_id'=>$analisis->id]);

                if( !isset( $ordenExamenes ) ){
                    $ordenExamenes= new Examen();
                    $ordenExamenes->analisis_id=$analisis->id;
                    $ordenExamenes->precio=$item['precio'];
                    $ordenExamenes->orden_id=$orden->id;
                    $ordenExamenes->save(false);
                }else{
                    $ordenExamenes->precio=$item['precio'];
                    $ordenExamenes->save(false);
                }
            }
            //  Elimina los Examenes que dejaron de seleccionarse
            foreach ( Examen::findAll(['orden_id'=>$orden->id]) as $examenInDB) {
                $debeSerBorrado=true;
                foreach ($items as $examen) {
                    if( $examenInDB->analisis_id == $examen['idProducto']){
                        $debeSerBorrado=false;
                    }
                }

                if($debeSerBorrado){
                    foreach ($examenInDB->examenParametros as $examenParametrosInDB) {
                        $examenParametrosInDB->delete();
                    }
                    $examenInDB->delete();
                }


            }

            // Cálculos finales
            $orden->fecha = new Expression('NOW()');
            $orden->cerrada = false;
            $orden->pagado = false;

            $orden->precio = Examen::find()->where(['orden_id' => $orden->id])->sum('precio');
            $orden->codigo_lab = isset($post['codigo_lab']) ? floatval($post['codigo_lab']) : 0;
            $orden->porcentaje_desc = isset($post['descuento']) ? floatval($post['descuento']) : 0;
            $orden->descuento = $orden->precio * ($orden->porcentaje_desc / 100);
            $orden->valor_total = $orden->precio - $orden->descuento;



            // Guardar actualización
            if (!$orden->save(false)) {
                Yii::error("Error actualizando orden post-cálculo: " . json_encode($orden->getErrors()), __METHOD__);
            }



            $transaction->commit();

            return $this->asJson(['success' => true, 'orden_id' => $orden->id]);

        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error("Error al guardar la orden: {$e->getMessage()}", __METHOD__);
            return $this->asJson(['success' => false, 'error' => 'Ocurrió un error al guardar la orden.']);
        }
    }

    public function actionClienteBuscar($q)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $clientes = User::find()->alias("u")
            ->innerJoin('auth_assignment','u.id=auth_assignment.user_id')
            ->where(['item_name'=>'cliente'])
            ->andWhere(['or',
                ['ilike', 'u.nombres', $q],
                ['ilike', 'identificacion', $q],
            ])
            ->limit(10) // Limita la búsqueda a los primeros 10 resultados
            ->all();

        return array_map(function ($c) {
            return [
                'id' => $c->id,
                'nombres' => $c->nombres,
                'identificacion' => $c->identificacion,
                'email' => $c->email,
                'email_notificacion' => $c->email_notificacion,
                'telefono' => $c->telefono,
                'direccion' => $c->direccion,
                'sexo_id' => $c->sexo_id,
                'fecha_nacimiento' => $c->fecha_nacimiento,
            ];
        }, $clientes);
    }


public function actionClienteGuardar()
{
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $data = \Yii::$app->request->post();
    $transaction = \Yii::$app->db->beginTransaction();

    try {
        if (isset($data['id']) && !empty($data['id'])) {
            $cliente = UserBussines::findOne($data['id']);
            if (!$cliente) {
                return ['success' => false, 'errors' => ['id' => ['Cliente no encontrado']]];
            }
            $isNew = false;
        } else {
            $cliente = new UserBussines();
            $cliente->username = $data['identificacion'] ?? '';
            $isNew = true;
        }

        // Asignar atributos con precaución
        $cliente->setAttributes([
            'nombres' => $data['nombres'] ?? '',
            'identificacion' => $data['identificacion'] ?? '',
            'email' => $data['email'] ?? '',
            'email_notificacion' => $data['email_notificacion'] ?? '',
            'telefono' => $data['telefono'] ?? '',
            'direccion' => $data['direccion'] ?? '',
            'fecha_nacimiento' => $data['fecha_nacimiento'] ?? '',
            'sexo_id' => $data['sexo_id'] ?? '',
        ]);

        if (!$cliente->save()) {
            return ['success' => false, 'errors' => $cliente->getErrors()];
        }

        if ($isNew) {
            $addrol = new AuthAssignment([
                'item_name' => 'cliente',
                'user_id' => $cliente->id,
            ]);
            if (!$addrol->save(false)) {
                throw new \Exception('No se pudo asignar el rol al cliente.');
            }

            Registro::onCreated($cliente);
        }

        $transaction->commit();

        return [
            'success' => true,
            'cliente' => [
                'id' => $cliente->id,
                'nombres' => $cliente->nombres,
                'identificacion' => $cliente->identificacion,
                'email' => $cliente->email,
                'email_notificacion' => $cliente->email_notificacion,
                'telefono' => $cliente->telefono,
                'direccion' => $cliente->direccion,
                'sexo_id' => $cliente->sexo_id,
                'fecha_nacimiento' => $cliente->fecha_nacimiento,
            ],
        ];
    } catch (\Throwable $e) {
        $transaction->rollBack();
        \Yii::error("Error al guardar cliente: " . $e->getMessage(), __METHOD__);
        return [
            'success' => false,
            'errors' => ['exception' => $e->getMessage()],
        ];
    }
}


    public function actionAnalisisBuscar($q)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $productos = Analisis::find()

            ->where(['activo'=>true])
            ->andWhere(['ilike', 'nombre', $q])
            ->limit(10)
            ->all();

        return array_map(function ($c) {
            return [
                'id' => $c->id,
                'descripcion' => $c->nombre,
                'precio' => number_format($c->precio),
            ];
        }, $productos);
    }

    /********************Fin de funciones UI ReactJS ******************/

    public function actionPrintTicket()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $post = Yii::$app->request->post();

            if (empty($post['ticket'])) {
                return ['success' => false, 'error' => 'ID de orden no proporcionado'];
            }

            $ordenId = $post['ticket'];
            $orden = OrdenBussines::findOne($ordenId);

            if (!$orden) {
                return ['success' => false, 'error' => 'Orden no encontrada'];
            }

            $ticket = new PDF_ORDEN_TICKET($orden);


            return [
                'success' => true,
                'pdf' => $ticket->printBase64()
            ];

        } catch (\Throwable $e) {
           Yii::error('Error generando ticket PDF: ' . $e->getMessage(), __METHOD__);
           return ['success' => false, 'error' => 'Ocurrió un error al generar el ticket.'];
       }
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
            $transaction->rollBack();
            throw new HttpException('500',"Error al cambiar el estado de la orden");
        }        
    }
    
    public function actionEnviarMail($id){
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $orden = OrdenBussines::find()->where(['id'=>$id])->one();
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
            $transaction->rollBack();
            throw new HttpException('500',$e->getMessage());
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
                        'query' => OrdenBussines::find()->where(['between', 'fecha', Yii::$app->request->post()['fecha_inicio'],  Yii::$app->request->post()['fecha_fin'] ]),
                        
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
                        'query' => ExamenBussines::find()
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



    public function actionFirmar($orden_id){
        if(Yii::$app->request->isPost){
            $orden= OrdenBussines::findOne($orden_id);
            return $orden->firmarDigitalmente();
        }
        return json_encode(["errors"=>true, "message"=>"No se ha  encontrado documento a firmar"]);
    }

    // ajax 
     public function actionFinalizar($orden_id){
         \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
         if(isset( $orden_id )){
             $orden=OrdenBussines::findOne($orden_id);
              return  $orden->finalizar();
            }  
     }
     
     public function actionPagar($orden_id){
         if(isset( $orden_id )){
             $orden=OrdenBussines::findOne($orden_id);
             return  $orden->pagar();
         }
     }
     
     public function actionDescuento($orden_id){
         if(isset( $orden_id )){
             $orden=OrdenBussines::findOne($orden_id);
             
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
        if(isset(Yii::$app->request->post('OrdenBussines')['_id'])){
            $model = OrdenBussines::findOne( Yii::$app->request->post('OrdenBussines')['_id']);
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

    public function actionGuardarResponsables(){
        if(isset(Yii::$app->request->post('OrdenBussines')['_id'])){
            $model = OrdenBussines::findOne( Yii::$app->request->post('OrdenBussines')['_id']);
            if( $model->load( Yii::$app->request->post() )  ){
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
        
        $examen = ExamenBussines::findOne($id);
        $examen->nota= trim ( Yii::$app->request->post()['ExamenBussines']['nota'] );
        $examen->save(false);
        return $examen->getForm();
        }
        return "El analisis que intentas ingresar resultado no tiene esquema ingresado";
    }
    
    
    //ajax
    public function actionExamenPlantilla($examen_id){
        $examen = ExamenBussines::findOne($examen_id);
        return $examen->getForm();
        
    }
    
    //ajax
    public function actionExamenes($orden_id){
        $this->layout = false;
        $orden = OrdenBussines::findOne($orden_id);
        $orden->_id =$orden->id;

        return $this->render('ingreso_resultados/_examenes',['orden'=>$orden]);

        
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
              <button class='btn btn-primary'>Guardar Prueba de Sensiblidad </button>
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
        $orden= OrdenBussines::findOne($id);                
        return $this->render('ver-resultado',['orden'=>$orden]);        
    }
    
    
      public function actionOnlineOrden($id){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $orden= OrdenBussines::findOne($id);
        if( !$orden->firmado_digitalmente){
            return $orden->pdf();
        }else{
           return Yii::$app->response->sendFile(__DIR__ . "/../../../media/ordenes/" .$orden->codigo.'.pdf',$orden->codigo.'.pdf' , ['inline' => true])->send();
        }        
    }
    
    public function actionImprimir($id){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $orden= OrdenBussines::findOne($id);
        if( !$orden->firmado_digitalmente){
            return $orden->pdf();
        }else{
           return Yii::$app->response->sendFile(__DIR__ . "/../../../media/ordenes/" .$orden->codigo.'.pdf',$orden->codigo.'.pdf' , ['inline' => true])->send();
        }        
    }
	


	
       
    

    public function actionIndex()
    {
        $GridModel = new OrdenGrid();
        $dataProvider = $GridModel->Grid(Yii::$app->request->queryParams);

        return $this->render('index', [
            'GridModel' => $GridModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * Lists all Orden models.
     * @return mixed
     */
    public function actionIndexConAnalisis()
    {
        $GridModel = new OrdenGrid();
        $dataProvider = $GridModel->Grid(Yii::$app->request->queryParams);
        
        return $this->render('index_con_analisis', [
            'GridModel' => $GridModel,
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



    public function actionNueva()
    {

        $model = OrdenBussines::nueva();
        if(isset($model->id)){
            return $this->redirect(['view', 'id' => $model->id]);

        }
        return $this->render('nueva-v2', [
            'model' => $model,
        ]);

    }




    public function actionEditarOld($id)
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
                    $model->firmado_digitalmente = false;
                    $model->fecha_firmado_digital = null;
                    $model->borrarDocumentoFirmado();
                    Registro::onUpdated($model);
                    
                    
                }
                
                
                $transaction->commit();
            }catch (Exception $e){
                $transaction->rollBack();
                return $this->render('editar', [
                    'model' => $model,
                ]);
                
            }
                      
            
            return $this->redirect(['view', 'id' => $model->id]);
        
        }
    
        return $this->render('editar', [
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


    protected function findModel($id)
    {
        if (($model = OrdenBussines::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

 function build_post_fields( $data,$existingKeys='',&$returnArray=[]){
    if(($data instanceof CURLFile) or !(is_array($data) or is_object($data))){
        $returnArray[$existingKeys]=$data;
        return $returnArray;
    }
    else{
        foreach ($data as $key => $item) {
            build_post_fields($item,$existingKeys?$existingKeys."[$key]":$key,$returnArray);
        }
        return $returnArray;
    }
}


