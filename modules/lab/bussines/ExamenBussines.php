<?php

namespace app\modules\lab\bussines;


use app\modules\lab\models\Analisis;
use app\modules\lab\models\Examen;
use app\modules\lab\models\ExamenGermen;
use app\modules\lab\models\ExamenParametro;
use app\modules\lab\models\Orden;
use Yii;
use yii\bootstrap\Html;

/**
 * This is the model class for table "examen".
 *
 * @property int $id
 * @property int $analisis_id
 * @property int $orden_id
 * @property string $precio
 * @property string $nota
 *
 * @property Analisis $analisis
 * @property Orden $orden
 * @property ExamenParametro[] $examenParametros
 * @property ExamenGermen[] $examenGermenes
 */
class ExamenBussines extends Examen
{
      public $_orden_cerrada;

    public function getNombreCosto()
    {
       
        return $this->analisis->nombre.' ('.$this->analisis->precio.')';
    }
    
    public function getForm() {       
        
        
        $form= Html::beginForm(['#'], 'post',
                                      ['id' =>'formularioResultados',
                                       'onsubmit'=>'return guardarResultado()',
                                        'class'=>'animated fadeIn'
                                      ]);
           $form.=Html::tag(
               'div', Html::tag(
                   'div', 'PARAMETROS', ['class' => 'col-md-3']
               ) . Html::tag(
                   'div', 'RESULTADO', ['class' => 'col-md-3']
               ) .Html::tag(
                       'div','UNIDADES',['class'=>'col-md-3']
                    ).Html::tag(
                       'div','REFERENCIA',['class'=>'col-md-3']
                        ),['class'=>'row']);
       $seccion='a';
       foreach ($this->analisis->parametrosParaIngresoResultado as $param) {
            
            $examenParametro=ExamenParametro::findOne(['examen_id'=>$this->id,'parametro_id'=>$param->id]);
            if(!isset($examenParametro)){
                $examenParametro=new ExamenParametro();
            }
         
         
            
            $medida='';
            if(isset($param->medida_id)){
               $medida= $param->medida->descripcion;
            }
            
            
            $input = $this->getInputValor($examenParametro,$param);
            
            if(isset($param->seccion_id) && $seccion != $param->seccion_id ){
                $form.=Html::tag(
                    'div',Html::tag(
                        'div',Html::tag('div',$param->seccion->descripcion,['class'=>'badge badge-info']),['class'=>'col-md-9'])
                    ,['class'=>'row']);
                
            }
            
            if(isset($param->metodo_id)){
                $form.=Html::tag(
                    'div', Html::tag(
                        'div', Html::label('METODO'), ['class' => 'col-md-3'])
                    . Html::tag(
                        'div', Html::label($param->metodo->descripcion), ['class' => 'col-md-9'])
                        ,['class'=>'row']);
                
            }
            
            $referencia = ExamenBussines::generarReferencia($param);
            //si es referencia selecionable
            $referenciaInput='';
            $referenciaHiddenInput='';
            if( trim( is_string($param->valores_referencia_seleccionable ) ? $param->valores_referencia_seleccionable  : '') !='' ){
                    $values = [];
                    $items = explode(",", $param->valores_referencia_seleccionable);
                    foreach ($items as $item) {                        
                        $values[$item] = $item;
                    }
                    $referenciaInput = Html::activeDropDownList($examenParametro, 'referencia', $values, [
                        'class' => 'form-control form-control-sm',
                        'prompt' => 'Seleccione',
                        'name' => 'referencia[]',
                        //'required' => ''
                    ]);
                }else{
                    $referenciaHiddenInput= Html::activeHiddenInput($examenParametro,'referencia',['name'=>'referencia[]','value'=>$referencia]);
                    $referenciaInput=$referencia;
                        }
            
            $form.=Html::tag(
                'div', Html::tag(
                    'div', Html::label($param->descripcion), ['class' => 'col-md-3']
                ) . Html::tag(
                    'div', $input . Html::activeHiddenInput($examenParametro, 'examen_id', ['name' => 'examen_id[]', 'value' => $this->id]) .
                    $referenciaHiddenInput .
                    Html::hiddenInput('medida[]', $medida) .
                    Html::activeHiddenInput($examenParametro, 'parametro_id', ['name' => 'parametro_id[]', 'value' => $param->id]), ['class' => 'col-md-3']
                ) .Html::tag(
                            'div',Html::label($medida),['class'=>'col-md-3']
                            ).Html::tag(
                                'div',Html::label($referenciaInput),['class'=>'col-md-3']
                                ),['class'=>'row']);
            $seccion = $param->seccion_id; 
            
        } 
        
        $form .='<div class="form-group row">
                    <label class="col-sm-2 col-form-label">Nota</label>
                    <div class="col-sm-10">
                      '. Html::activeTextarea( $this, 'nota', ['rows'=>5,'cols'=>12, 'class' => 'form-control']).'
                    </div>
                  </div>';
     
        
        $form.=Html::submitButton('Guardar',['class'=>'btn btn-primary mt-3 mr-2']);
                if($this->analisis->tipo_analisis_id==11){
                    
                    $form.=Html::button('Agregar Germen',
                        ['class'=>'btn btn-secondary  mt-3' ,
                         'data-toggle'=>'modal',
                         'data-target'=>'#modalGermen',
                         'onclick'=>"$('#md_germen_id').val($this->id);",
                        ]);
                    
                }
        
        $form .= Html::endForm();
        
        foreach ($this->examenGermenes as $examenGermen) {            
            
            $form.='<div class="card border-primary mt-2">
                      <div class="card-body">';
            
            $form.=Html::tag(
                'div', Html::tag(
                    'div', Html::label('Identificacion del Germen'), ['class' => 'col-md-3']
                ) . Html::tag(
                    'div', isset($examenGermen->germen_id) ? $examenGermen->germen->descripcion : '', ['class' => 'col-md-9']
                ),['class'=>'row']);
            
            $form.=Html::tag(
                'div', Html::tag(
                    'div', Html::label('Contaje Bacteriano'), ['class' => 'col-md-3']
                ) . Html::tag(
                    'div', $examenGermen->contaje_colonia . ' [UFC/ML]', ['class' => 'col-md-9']
                ),['class'=>'row']);
            
            $form.='     <h5 class="card-title">Prueba de Sensibilidad Bacteriana</h5>';
            $form.='     <div class="col-12" id="content_germen'.$examenGermen->id.'"></div>';
            
            
            
            $form.="<div class='col-12 text-right'>";
            $form.=Html::button('Borrar Germen',
                ['class'=>'btn btn-danger' ,
                    'data-toggle'=>'modal',
                    'data-target'=>'#borrarGermenModal',
                    'onclick'=>" $('#mdBorrar_germen_id').val($examenGermen->id);
                                 $('#mdBorrar_germen_examen_id').val($examenGermen->examen_id);
                                $('#mdBorrar_germen').val('". (isset( $examenGermen->germen_id )? $examenGermen->germen->descripcion :'' )."');",
                ]);
            
            $form.=' </div>
                     </div>
                     </div>';
            $form.="<script> plantillaPruebaSensibilida($examenGermen->id) </script>";
        }
       
        return $form;
    }


/**
 * Metodo que genera el componente para ingresar el valor del parametro
 * @param  $examenParametro
 * @param  $param
 * @return @input Input
 */
    private function getInputValor($examenParametro, $param)
    {
        if ($param->valores_posibles != '') {
            $values = [];
            $items = explode(",", $param->valores_posibles);
            foreach ($items as $item) {

                $values[$item] = $item;
            }
            $input = Html::activeDropDownList($examenParametro, 'valor', $values, [
                'class' => 'form-control form-control-sm',
                'prompt' => 'Seleccione',
                'name' => 'valor[]',
                //'required' => ''
            ]);
        } else {
            $input = Html::activeInput('text', $examenParametro, 'valor', [
                'class' => 'form-control form-control-sm',
                'name'=>'valor[]',
               // 'required'=>''
                
            ]);
            }
            return $input;
       }

    
    /**
     * Metodo que construye la referencia de un parametro 
     * @param  $param
     * @return string
     */
    public static function generarReferencia($param)
    {
        $referencia ='';
        if(!empty($param->unico_valor_referencial)){
            $referencia = $param->unico_valor_referencial;
        }elseif(Yii::$app->user->identity->sexo_id==1){            
            $referencia=$param->hombre_valo_de_referencia_min.' - '.$param->hombre_valo_de_referencia_max;
        }else{
            $referencia=$param->mujer_valo_de_referencia_min.' - '.$param->mujer_valo_de_referencia_max;
        }
        
        if( ( Yii::$app->user->identity->edad < 13 ) && trim( $param->ninio_valo_de_referencia_min ) !='' ){
            $referencia=$param->ninio_valo_de_referencia_min.' - '.$param->ninio_valo_de_referencia_max ;
        }
        if(trim($referencia)=='-'){
            $referencia='';
        }
        return $referencia;
    }

    
    
    public function getResultado() {  
        $form= Html::beginForm(['orden/imprimir-resultado'], 'post');
        $form.=Html::tag(
            'div', Html::tag(
                'div', '<strong>PARAMETROS</strong>', ['class' => 'col-md-3']
            ) . Html::tag(
                'div', '<strong>RESULTADO</strong>', ['class' => 'col-md-3']
            ) .Html::tag(
                        'div','<strong>UNIDADES</strong>',['class'=>'col-md-3']
                        ).Html::tag(
                            'div','<strong>REFERENCIA</strong>',['class'=>'col-md-3']
                            ),['class'=>'row', ]);
        $examenParametros=$this->examenParametros;
        foreach ($examenParametros as $examenParametro) {
            $param= $examenParametro->parametro;
            $medida=$examenParametro->medida;
                
            $input=  Html::label($examenParametro->valor,['class'=>'form-control']);
            
                if(isset($param->metodo_id)){
                    $form.=Html::tag(
                        'div', Html::tag(
                            'div', Html::label('METODO'), ['class' => 'col-md-3'])
                        . Html::tag(
                            'div', Html::label($param->metodo->descripcion), ['class' => 'col-md-9'])
                        ,['class'=>'row']);
                    
                }
            
            $form.=Html::tag(
                'div', Html::tag(
                    'div', Html::label('<strong>' . $param->descripcion . '</strong>'), ['class' => 'col-md-3']
                ) . Html::tag(
                    'div', $input, ['class' => 'col-md-3']
                ) .Html::tag(
                            'div',Html::label($medida),['class'=>'col-md-3']
                            ).Html::tag(
                                'div',$examenParametro->referencia,['class'=>'col-md-3']
                                ),['class'=>'row','style'=>'border-bottom: 1px dashed #999;']);
            
        }        
      
        $form.=Html::endForm();
        
        foreach ($this->examenGermenes as $examenGermen) {
            
            
            $form.='<div class="card border-primary mt-2">
                      <div class="card-body text-primary">';
            
            $form.=Html::tag(
                'div', Html::tag(
                    'div', Html::label('Identificacion del Germen'), ['class' => 'col-md-3']
                ) . Html::tag(
                    'div', $examenGermen->germen_id ? $examenGermen->germen->descripcion : '', ['class' => 'col-md-9']
                ),['class'=>'row']);
            
            $form.=Html::tag(
                'div', Html::tag(
                    'div', Html::label('Contaje de Colonia'), ['class' => 'col-md-3']
                ) . Html::tag(
                    'div', $examenGermen->contaje_colonia, ['class' => 'col-md-9']
                ),['class'=>'row']);
            
            $form.='     <h5 class="card-title">Prueba de Sensibilidad Bacteriana</h5>';
            $form.='     <div class="col-12" id="content_germen'.$examenGermen->id.'"></div>';
            
            $form.= $examenGermen->verPruebaSensibilidadExamenGermen();            
            
            
            $form.="<div class='col-12 text-right'>";           
            
            $form.=' </div>
                     </div>
                     </div>';
          }
        
        
        
        return $form;
        
    }
}
