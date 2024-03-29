<?php

use app\models\ExamenGermen;
use app\models\Germen;
use app\models\Orden;
use kartik\select2\Select2;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\assets\IngresoResultadoPageAsset;
use app\assets\JQueryClockTimePicker;
use app\assets\JQueryClockTimePickerAsset;
use app\assets\OrdenPageAsset;
use app\assets\JSLoadingOverlayAsset;
use yii\jui\JuiAsset;
/* @var $this yii\web\View */
/* @var $model app\models\Orden */
/* @var $form yii\widgets\ActiveForm */


IngresoResultadoPageAsset::register($this);
OrdenPageAsset::register($this);
JSLoadingOverlayAsset::register($this);
JuiAsset::register($this);
JQueryClockTimePickerAsset::register($this);
?>
<div class='row'> 
<div class='col-12'>
  <div class='panel panel-primary'>
    <div class='panel-heading'>Ordenes</div>    
      <div class='panel-body'>    
     
          <?php 
          echo Select2::widget([
              'name' => 'orden',
              'data' =>  ArrayHelper::map(Orden::findAll(['cerrada'=>false,]), 'id', 'detalle'),
              'options' => [
                  'placeholder' => 'Seleccionar ...',
                  'multiple' => false,
                  'onChange'=>'cargarExamenes()',
                  'id'=>'cbox_orden'
              ],
          ]);
          
    
          ?>
      </div>          
   </div>
</div>
</div>

<div class='row' id="examenes">
   
</div>




<!-- Modal Utilizado para agregar germenes a los cultivos -->
<?php Modal::begin([
    'id' => 'modalGermen',
    'header' => 'Germenes',

]);?>

 <?php $form = ActiveForm::begin(['method' => 'post','options'=>[ 'onsubmit' => 'return guardarGermen()','id' => 'form-add-germen']]);
 $germen = new ExamenGermen(); ?>

  <?php echo $form->field($germen, 'examen_id')->hiddenInput(['id'=>'md_germen_id'])->label(false);?>

  <?php echo $form->field($germen, 'germen_id')->dropDownList(
      ArrayHelper::map(Germen::find()->All(), 'id', 'descripcion'),
      ['prompt'=>'Seleccionar ...']
      
              );?>
              
     <?php echo $form->field($germen, 'contaje_colonia')->textInput();?>
    

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

<?php Modal::end();?>



<!-- Modal Utilizado para borrar germenes a los cultivos -->
<?php Modal::begin([
    'id' => 'borrarGermenModal',
    'header' => 'Borrar Germen',

]);?>

 <?php $form = ActiveForm::begin(['options' =>['id'=>'formularioBorrarGermen','onsubmit'=>'return borrarGermen()']]); $germen = new ExamenGermen(); ?>

  <?php echo $form->field($germen, '_id')->hiddenInput(['id'=>'mdBorrar_germen_id'])->label(false);?>
   <?php echo $form->field($germen, 'examen_id')->hiddenInput(['id'=>'mdBorrar_germen_examen_id'])->label(false);?>
  
  
  
  <p>Esta seguro que dese borrar el germen</p>
  <?php echo Html::textInput('name','',[ 'class'=>"form-control mt-2", 'readonly'=>true,'id'=>'mdBorrar_germen'])?>

    <div class="form-group">
        <?= Html::submitButton('Borrar', ['class' => 'btn btn-danger mt-3']) ?>
    </div>

    <?php ActiveForm::end(); ?>

<?php Modal::end();?>


