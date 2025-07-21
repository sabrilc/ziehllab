<?php

namespace app\modules\lab\bussines;

use app\modules\lab\models\Laboratorista;
use app\modules\lab\models\Orden;
use app\modules\lab\pdfs\PDF_ORDEN_ACCESS;
use app\modules\site\bussines\UserBussines;
use app\modules\site\models\User;
use utils\Tools;
use Yii;
use yii\db\Exception;
use yii\db\Expression;


class OrdenBussines extends Orden
{
    
    public $resultado;
    
    public $_id;

    public $_examenes;

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaciente()
    {
        return $this->hasOne(UserBussines::class, ['id' => 'paciente_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExamens()
    {
        return $this->hasMany(ExamenBussines::class, ['orden_id' => 'id']);
    }



    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLaboratorista()
    {
        return $this->hasOne(Laboratorista::class, ['id' => 'laboratorista_id']);
    }

    public function getResponsableTecnico()
    {
        return $this->hasOne(Laboratorista::class, ['id' => 'responsable_tecnico_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor()
    {
        return $this->hasOne(User::class, ['id' => 'doctor_id']);
    }

    public function getDetalle(){
        return $this->fecha.' | '. $this->codigo.' - '. ( (isset( $this->paciente_id))?$this->paciente->nombreCompleto : '' ) ;
    }


    public function getExamenesParaImprimir(){
      return  $this->getExamens()->joinWith(['analisis'])->orderBy(['hoja_impresion'=>SORT_ASC,'orden_impresion'=>SORT_ASC]);
    }
    
    public function pdf($for_signer=false, $binary=false) {

         if( $for_signer){
             $pdf= new PDF_ORDEN_ACCESS($this, true);
             return ($pdf->Output('','S'));
         }else{
             $pdf= new PDF_ORDEN_ACCESS($this);
			 if($binary){
				  return ($pdf->Output('','S'));
			 }
			 else{
				 return $pdf->Output('','ORDEN_'.$this->codigo.'.pdf'); 
			 }
            
         }


    }
    
    public function pdfForDownload() {
		if( !$this->firmado_digitalmente){
			 $pdf= new PDF_ORDEN_ACCESS($this);
              return $pdf->Output('','S');
        }else{$contenido =null;
		 try{
			$contenido = file_get_contents(__DIR__ . "/../media/ordenes/" .$this->codigo.'.pdf',$this->codigo.'.pdf');
		 }catch(\Exception $e){}
           return $contenido;
        }
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
            
            
            $mailTemplate = new \app\modules\lab\models\OrdenMailTemplate( $this);
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
        $model = new OrdenBussines();
        $model->setScenario('nuevo');
        try {
                if (isset(Yii::$app->request->post()['analisis']) && $model->load(Yii::$app->request->post()) && $model->save()) {
                    $examenesSeleccionados=  Yii::$app->request->post()['analisis'];
                    foreach ($examenesSeleccionados as $examen) {
                        $analisis= \app\modules\lab\models\Analisis::findOne($examen);
                        $ordenExamen= new \app\modules\lab\models\Examen();
                        $ordenExamen->analisis_id=$analisis->id;
                        $ordenExamen->precio=$analisis->precio;
                        $ordenExamen->orden_id=$model->id;
                        $ordenExamen->save(false);
                        
                        $parametros= $analisis->parametros;
                        foreach ($parametros as $parametro) {
                            $examenParametro=new \app\modules\lab\models\ExamenParametro();
                            $examenParametro->examen_id=$ordenExamen->id;
                            $examenParametro->parametro_id=$parametro->id;
                            $examenParametro->save(false);
                        }
                        
                        
                    }
                    
                    $model->fecha= new Expression('now()');
                    $model->codigo=str_pad($model->id, 10, "0", STR_PAD_LEFT);
                    $model->cerrada=false;
                    $model->pagado = false;
                    $model->precio= \app\modules\lab\models\Examen::find()->where(['orden_id'=>$model->id])->sum('precio');
                    $model->descuento=0;
                    $model->valor_total = $model->precio;
                    
                    $model->codigo_secreto = OrdenBussines::getCodigoSecreto();
                    $model->generateToken();
                    \app\modules\lab\models\Registro::onCreated($model);
                                       
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
    
    public function generateToken() {
        $this->token = base64_encode( sha1(uniqid($this->codigo,true), false) );
    }

    public function firmarDigitalmente(){
        $this->fecha_firmado_digital = date("Y-m-d H:i:s" );
        $this->firmado_digitalmente = true;
        $pdf_binary = $this->pdf(true);
        $tmpfname = tempnam('', "LAB");
        $handle = fopen($tmpfname, "w");
        fwrite($handle, $pdf_binary);
        fclose($handle);
        $url = \Yii::$app->params['signature_api'].'/api/sign/pdf';
        $files = [ $tmpfname];
        $secrets=[$this->fecha_firmado_digital];
        if( is_null( $this->laboratorista_id ) || $this->laboratorista_id == 0){
            return json_encode(["errors"=>true, "message"=> "La orden No.".$this->codigo." debe tener asignado un laboratorista"]);
        }
        $laboratorista =$this->laboratorista;

        if ( !Tools::validP12File( __DIR__ . "/ziehllab/" .$laboratorista->dir_firma_digital)){
            return json_encode(["errors"=>true, "message"=> $laboratorista->nombres. " no tiene firma digital"]);
        }
        $files[]= __DIR__ . "/ziehllab/" .$laboratorista->dir_firma_digital;
        $secrets[]=$laboratorista->firma_digital_secret;


        if( is_null( $this->responsable_tecnico_id )  || $this->responsable_tecnico_id == 0){
            return json_encode(["errors"=>true, "message"=> "La orden No.".$this->codigo." debe tener asignado un responsable técnico"]);
        }

        $tecnico =$this->responsableTecnico;
        if ( !Tools::validP12File( __DIR__ . "/ziehllab/" .$tecnico->dir_firma_digital)){
            return json_encode(["errors"=>true, "message"=> $tecnico->nombres. "no tiene firma digital"]);
        }
        $files[]= __DIR__ . "/ziehllab/" .$tecnico->dir_firma_digital;
        $secrets[]=$tecnico->firma_digital_secret;

        foreach ($files as $index => $file) {
            $postData['file_'.$index]= curl_file_create(realpath($file), mime_content_type($file),  basename($file));
        }

        foreach ($secrets as $index=> $secret){
            $postData['secret_'.$index] = $secret;
        }



        $request = curl_init($url);
        curl_setopt($request, CURLOPT_POST, true);
        curl_setopt($request, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_VERBOSE, 0);
        $result = curl_exec($request);

        if ($result === false) {
            error_log(curl_error($request));
        }

        curl_close($request);

        $response= json_decode($result);
        Tools::removeFile($tmpfname);
        if ( $response->errors){
            return $result;
        }else{
            $fp = fopen(__DIR__ . '/../media/ordenes/' . $this->codigo.'.pdf', 'w');
            fwrite($fp, base64_decode($response->pdf));
            fclose($fp);
            $this->save();
            return json_encode(["errors"=>false, "message"=>"Documento Firmado Satisfactoriamente"]);
        }

    }

    public function borrarDocumentoFirmado(){
        Tools::removeFile(__DIR__ . '/../../../media/ordenes/' . $this->codigo.'.pdf');
        $this->firmado_digitalmente = false;
        $this->fecha_firmado_digital = null;
        $this->save();
    }
   
}
