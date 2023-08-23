<?php

namespace app\models;

use Yii;
use yii\db\Exception;
use yii\db\Expression;

/**
 * This is the model class for table "orden".
 *
 * @property int $id
 * @property string $codigo
 * @property string $codigo_secreto
 * @property string $fecha
 * @property string $precio
 * @property string $descuento
 * @property string $valor_total
 * @property string $abono
 * @property int $pagado
 * @property int $cerrada
 * @property int $paciente_id
 * @property int $doctor_id
 * @property int $laboratorista_id
 * @property int $cotizacion_id
 * @property int $email_enviado
 * @property string $fecha_email_enviado
 * @property string $token
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property Cotizacion[] $cotizacions
 * @property Examen[] $examens
 * @property User $paciente
 * @property User $doctor
 * @property Cotizacion $cotizacion
 */
class Orden extends \yii\db\ActiveRecord
{
    
    public $resultado;
    
    public $_id;
    

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orden';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fecha', 'fecha_email_enviado', 'created_at', 'updated_at','fecha_resultados','hora_resultados','_id'], 'safe'],
            [['precio', 'descuento', 'valor_total', 'abono'], 'number'],
            [['pagado', 'cerrada','paciente_id', 'doctor_id', 'laboratorista_id', 'cotizacion_id', 'email_enviado', 'created_by', 'updated_by'], 'integer'],
            [['codigo'], 'string', 'max' => 10],
            [['codigo_secreto'], 'string', 'max' => 6],
            [['token'], 'string', 'max' => 100],
            [['paciente_info', 'solicitante_info'], 'string', 'max' => 100],
            [['paciente_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['paciente_id' => 'id']],           
            [['doctor_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['doctor_id' => 'id']],
            [['cotizacion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cotizacion::className(), 'targetAttribute' => ['cotizacion_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codigo' => 'Codigo',
            'fecha' => 'Fecha',
            'precio' => 'Precio',
            'descuento' => 'Descuento',
            'valor_total' => 'Valor Total',
            'abono' => 'Abono',
            'pagado' => 'Pagado',
            'cerrada' => 'Cerrada',
            'paciente_id' => 'Paciente',
            'doctor_id' => 'Doctor',
            'cotizacion_id' => 'Cotizacion',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'laboratorista_id'=>'Laboratorista',
            'paciente_info'=>'Información del paciente',
            'solicitante_info'=>'Información del solictante de la prueba',
         
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCotizacions()
    {
        return $this->hasMany(Cotizacion::className(), ['orden_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExamens()
    {
        return $this->hasMany(Examen::className(), ['orden_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCultivos()
    {
    return $this->getExamens()->joinWith(['analisis'])->where(['analisis.tipo_analisis_id'=>11]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaciente()
    {
        return $this->hasOne(User::className(), ['id' => 'paciente_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLaboratorista()
    {
        return $this->hasOne(Laboratorista::className(), ['id' => 'laboratorista_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor()
    {
        return $this->hasOne(User::className(), ['id' => 'doctor_id']);
    }
    
    public function getDetalle(){
        return $this->fecha.' | '. $this->codigo.' - '. ( (isset( $this->paciente_id))?$this->paciente->nombreCompleto : '' ) ;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCotizacion()
    {
        return $this->hasOne(Cotizacion::className(), ['id' => 'cotizacion_id']);
    }
    
    public function getExamenesParaImprimir(){
      return  $this->getExamens()->joinWith(['analisis'])->orderBy(['hoja_impresion'=>SORT_ASC,'orden_impresion'=>SORT_ASC]);
    }
    
    public function pdf() {
		if ($this->id == 17155 ||  $this->id == 22560 || $this->id == 17118 || $this->id == 17146 || $this->id == 17159 || $this->id == 22709){ 
		 $pdf= new PDF_ORDEN_ACCESS($this->id);
         $pdf->Output('','ORDEN_'.$this->codigo.'.pdf');
		}else{
        // $pdf= new PDF_ORDEN($this->id);
		 $pdf= new PDF_ORDEN_ACCESS($this->id);
        $pdf->Output('','ORDEN_'.$this->codigo.'.pdf');
		}
    }
    
    public function pdfForDownload() {
        $pdf= new PDF_ORDEN($this->id);
        return $pdf->Output('','S');
    }
    
    public function pdfBinario() {
        $pdf= new PDF_ORDEN($this->id);
        return base64_encode($pdf->Output('','S'));
    }
    
 
    
    public function finalizar() {
       $mensaje='';
       $puedeFinalizar=true;
        $examenes = $this->examens;
        foreach ($examenes as $examen) {
            
           $parametros= $examen->examenParametros;
           $parametroVacio=false;
           foreach ($parametros as $parametros) {
               if(!isset($parametros->valor)){
                   $parametroVacio=true;
               }
               
           }
           
           if($parametroVacio==true){
               $mensaje.= ', '.$examen->analisis->nombre;
              $puedeFinalizar=false;
           }
        }
        
        if(strlen( $mensaje) > 3){ 
            return ['error'=>'Ingrese primero los resultados de'.$mensaje.' para poder finalizar..!' ];
        }
        
        if($puedeFinalizar==true){
            $this->cerrada=true;
            $this->save(false);
          return $this->enviarMail();
            
           
        }
              
    }
    
    public function enviarMail(){
        if( $this->pagado != true ){
            return ['warning'=>'No se envió el mail porque la orden no esta pagada, pero la orden ha sido finaliza con éxito..!'];
        }
       
        $email = $this->paciente->email_notificacion; 
        $validator = new \yii\validators\EmailValidator();
        if ($validator->validate($email)) {
            
            $mail= Yii::$app->mailer->compose()
            ->setFrom('notificacion@ziehllab.com')
            ->setTo($email)
            ->setSubject('Resultado de análisis');
            
            
            $mailTemplate = new OrdenMailTemplate( $this);
            $sent=$mail->setHtmlBody( $mailTemplate->html($mail))
            ->attachContent($this->pdfForDownload(),['fileName'=>'Analisis '.$this->codigo.'.pdf','base64','application/pdf'])
            ->send();
            
            if($sent){
                $this->email_enviado = true;
                $this->fecha_email_enviado = new Expression("now()");
                $this->save(false);
                return ['success'=>'Mail enviado y orden finalizada con éxito..!'];
            }else{
                return ['warning'=>'No se pudo enviar el mail, pero la orden ha sido finaliza con éxito..!'];
            }
        } else {
            return ['warning'=>'No se pudo enviar el mail, porque el correo es inválido, pero la orden ha sido finaliza con éxito..!'];
           
        }
        
       
    }
    
    public function pagar() {
            $this->pagado=true;
            return $this->save(false);
        }
        
        public function descuento() {
            $this->descuento= (float) $_POST['descuento'];
            $this->valor_total = $this->precio - $this->descuento;            
            return $this->save(false);
        }
    
    public static  function nueva() {
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {           
            $model = new Orden();
            if(isset(Yii::$app->request->post()['analisis'])){
                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    $examenesSeleccionados=  Yii::$app->request->post()['analisis'];
                    foreach ($examenesSeleccionados as $examen) {
                        $analisis=Analisis::findOne($examen);
                        $ordenExamen= new Examen();
                        $ordenExamen->analisis_id=$analisis->id;
                        $ordenExamen->precio=$analisis->precio;
                        $ordenExamen->orden_id=$model->id;
                        $ordenExamen->save(false);
                        
                        $parametros= $analisis->parametros;
                        foreach ($parametros as $parametro) {
                            $examenParametro=new ExamenParametro();
                            $examenParametro->examen_id=$ordenExamen->id;
                            $examenParametro->parametro_id=$parametro->id;
                            $examenParametro->save(false);
                        }
                        
                        
                    }
                    
                    $model->fecha= new Expression('now()');
                    $model->codigo=str_pad($model->id, 10, "0", STR_PAD_LEFT);
                    $model->cerrada=false;
                    $model->pagado = false;
                    $model->precio=Examen::find()->where(['orden_id'=>$model->id])->sum('precio');
                    $model->descuento=0;
                    $model->valor_total = $model->precio;
                    
                    $model->codigo_secreto = Orden::getCodigoSecreto();
                    $model->generateToken();
                    Registro::onCreated($model);
                                       
                }
                
            }
                     
            
            $transaction->commit();
        } catch(Exception $e) {
            $transaction->rollback();
        }
        
        return $model;
        
    }
    
    public  static  function getCodigoSecreto() {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
         return substr(str_shuffle($permitted_chars), 0, 6);
    }
    
    private function generateToken() {
        $this->token = base64_encode( sha1(uniqid($this->codigo,true), false) );
    }
   
}
