<?php

use app\modules\lab\models\ExamenGermen;
use app\modules\lab\models\Germen;
use app\modules\lab\bussines\OrdenBussines;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\assets\IngresoResultadoPageAsset;
use app\assets\JQueryClockTimePickerAsset;
use app\assets\OrdenPageAsset;
use app\assets\JSLoadingOverlayAsset;
use yii\jui\JuiAsset;
use app\assets\ReactJsAsset;


IngresoResultadoPageAsset::register($this);
OrdenPageAsset::register($this);
JSLoadingOverlayAsset::register($this);
JuiAsset::register($this);
JQueryClockTimePickerAsset::register($this);
ReactJsAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\modules\lab\models\Orden */
/* @var $form yii\widgets\ActiveForm */


?>
<div class='row'> 
<div class='col-12'>
  <div class='panel panel-primary'>
    <div class='panel-heading'>Ordenes</div>    
      <div class='panel-body'>    
     
          <?php 
              echo  Html::dropDownList('orden','',
              ArrayHelper::map(OrdenBussines::findAll(['cerrada'=>false,]), 'id', 'detalle'),
              [
                  'prompt' => 'Seleccionar ...',
                  'multiple' => false,
                  'onChange'=>'cargarExamenes()',
                  'class'=>'form-control select2me',
                  'id'=>'cbox_orden'
              ]
          )
    
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




<script type="text/babel" src="<?= \yii\helpers\Url::to('@web/static/js/orden.print.pdf.jsx') ?>"></script>