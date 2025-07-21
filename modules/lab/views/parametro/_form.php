<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Metodo;
use app\models\Medida;
use app\models\Seccion;

/* @var $this yii\web\View */
/* @var $model app\models\Parametro */
/* @var $form yii\widgets\ActiveForm */
?>

 <?php $form = ActiveForm::begin(); ?>
<div class="row">

   
   
   <div class="col-md-6">
     <?= $form->field($model, 'seccion_id')->dropDownList(ArrayHelper::map(Seccion::find()->where(['analisis_id'=>$model->analisis_id])->all(), 'id', 'descripcion'),
      ['prompt'=>'Seleccionar ...']);?>
   
   </div> 
   
    <div class="col-md-6">
     <?php
     $datos=[];
     for ($i = 1; $i < 25; $i++) { $datos[$i] = $i;};
    echo  $form->field($model, 'orden_impresion')->dropDownList($datos,
      ['prompt'=>'Seleccionar ...']);?>
   
   </div> 
   
     <div class="col-md-4">
         <?= $form->field($model, 'descripcion')->textInput(['maxlength' => true]) ?>
      </div>
     
      <div class="col-md-4">
         <?= $form->field($model, 'metodo_id')->dropDownList(ArrayHelper::map(Metodo::find()->all(), 'id', 'descripcion'),
      ['prompt'=>'Seleccionar ...']);?>
      </div>
       <div class="col-md-4">
        <?= $form->field($model, 'medida_id')->dropDownList(ArrayHelper::map(Medida::find()->all(), 'id', 'descripcion'),
      ['prompt'=>'Seleccionar ...']);?>
       </div>
     
	 <div class="col-md-12">
       <span class="badge badge-primary"><h3>Ensayo</h3></span>
       <?= $form->field($model, 'ensayo')->textarea(['rows' => '6','maxlength' => true,'placeholder'=> 'Se muestra unicamente en el formato AccessLab'])->label(false) ?>
       </div>
	   
	   <div class="col-md-12">
       <span class="badge badge-primary"><h3>Aplificación y detección</h3></span>
       <?= $form->field($model, 'amplificacion_deteccion')->textarea(['rows' => '6','maxlength' => true,'placeholder'=> 'Se muestra unicamente en el formato AccessLab'])->label(false) ?>
       </div>
	   
       <div class="col-md-12">
       <span class="badge badge-primary"><h3>Límite de detección</h3></span>
       <?= $form->field($model, 'limite_deteccion')->textInput(['maxlength' => true,'placeholder'=> 'Se muestra unicamente en el formato AccessLab'])->label(false) ?>
       </div>
    
      <div class="col-md-12">
       <span class="badge badge-primary"><h3>Referencia Seleccionable</h3></span>
       <?= $form->field($model, 'valores_referencia_seleccionable')->textarea(['rows' => '6','maxlength' => true,'placeholder'=> 'Si tiene multiples  valores refereciales llene este casillero con las unicas referencias(ref1, ref2, ref3, ..), omita llenar los otros casilleros de  referencias'])->label(false) ?>
       </div>
    
      <div class="col-md-12">
       <span class="badge badge-primary"><h3>Unico valor referencial</h3></span>
       <?= $form->field($model, 'unico_valor_referencial')->textarea(['rows' => '6','maxlength' => true,'placeholder'=> 'Si tiene un unico valor referecial llene este casillero con ese valor, omita llenar los otros casilleros de  referencias'])->label(false) ?>
       </div>
      
       <div class="col-md-12 text-center">
       <h1 class="">Valores Referenciales </h1></div>
       
        <div class="col-md-12">
         <h4>Hombres</h4>
        <div class="row">        
            <div class="col-md-5 mt-3">
              <?= $form->field($model, 'hombre_valo_de_referencia_min')->textInput(['maxlength' => true,'placeholder'=> 'Min'])->label(false) ?>
            </div> 
        <div class="col-md-1 text-center"> <h1 class=" mt-3"> A</h1></div>
        <div class="col-md-5  mt-3">
         <?= $form->field($model, 'hombre_valo_de_referencia_max')->textInput(['maxlength' => true,'placeholder'=> 'Max'])->label(false) ?>
         </div>
        </div> 
         </div>
      
      
        <div class="col-md-12">
         <h4>Mujeres</h4>
        <div class="row">        
            <div class="col-md-5 mt-3">
              <?= $form->field($model, 'mujer_valo_de_referencia_min')->textInput(['maxlength' => true,'placeholder'=> 'Min'])->label(false) ?>
            </div> 
        <div class="col-md-1 text-center"> <h1 class=" mt-3"> A</h1></div>
        <div class="col-md-5  mt-3">
         <?= $form->field($model, 'mujer_valo_de_referencia_max')->textInput(['maxlength' => true,'placeholder'=> 'Max'])->label(false) ?>
         </div>
        </div> 
         </div>
         
         
            <div class="col-md-12">
         <h4>Ninos</h4>
        <div class="row">        
            <div class="col-md-5 mt-3">
              <?= $form->field($model, 'ninio_valo_de_referencia_min')->textInput(['maxlength' => true,'placeholder'=> 'Min'])->label(false) ?>
            </div> 
        <div class="col-md-1 text-center"> <h1 class=" mt-3"> A</h1></div>
        <div class="col-md-5  mt-3">
         <?= $form->field($model, 'ninio_valo_de_referencia_max')->textInput(['maxlength' => true,'placeholder'=> 'Max'])->label(false) ?>
         </div>
        </div> 
         </div>
    
     
  
   
   <div class="col-md-12">
       <span class="badge badge-primary"><h3>Respuestas</h3></span>
       <?= $form->field($model, 'valores_posibles')->textarea(['rows' => '12','maxlength' => true,'placeholder'=> 'Solo llene este casillero con las unicas respuestas( res1, res2 , res3, ..), omita llenar si las respuestas son personalizadas'])->label(false) ?>
       </div>

    
   

  

    <?= $form->field($model, 'analisis_id')->hiddenInput()->label(false) ?>


    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

  

</div>

  <?php ActiveForm::end(); ?>