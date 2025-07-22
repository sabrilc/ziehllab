<?php

use app\modules\lab\models\Analisis;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\lab\models\TipoMuestra;
use app\modules\lab\models\TipoAnalisis;

/* @var $this yii\web\View */
/* @var $model Analisis */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row">

    <?php $form = ActiveForm::begin(); ?>

   <div class="col-md-12">
    <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>
   </div>
   <div class="col-md-6">
    <?= $form->field($model, 'descripcion')->textInput(['maxlength' => true]) ?>
   </div>
   
<div class="col-md-6">
<?= $form->field($model, 'precio')->widget(\yii\widgets\MaskedInput::class, [
        'clientOptions' => [
            'alias' => 'decimal',
            'digits' => 2,
            'digitsOptional' => false,
            'radixPoint' => '.',
            //'groupSeparator' => ',',
            'autoGroup' => true,
            'removeMaskOnSubmit' => true,
        ]]);?>
</div>  

  	<div class="col-md-6">
      <?= $form->field($model, 'tipo_muestra_id')->dropDownList(ArrayHelper::map(TipoMuestra::find()->all(), 'id', 'descripcion'),
        ['prompt'=>'Seleccionar...' ]);?>
	</div>
	
	<div class="col-md-6">
      <?= $form->field($model, 'tipo_analisis_id')->dropDownList(ArrayHelper::map(TipoAnalisis::find()->all(), 'id', 'descripcion'),
          ['prompt'=>'Seleccionar...' ]);?>
 	</div>

	<div class="col-md-6">         
          <?PHP
          $datos=[];
          for ($i = 1; $i < 100; $i++) { $datos[$i] = $i;};
          
         echo $form->field($model, 'hoja_impresion')->dropDownList($datos,
          ['prompt'=>'Seleccionar...' ]);?>
     </div>
     
 		<div class="col-md-6">  
          <?= $form->field($model, 'orden_impresion')->dropDownList($datos,
          ['prompt'=>'Seleccionar...' ]);?>
       </div>
       
	<div class="col-md-6">        
          <?= $form->field($model, 'activo')->checkbox() ?>
	</div>
	
	<div class="col-md-12">        
          <?= $form->field($model, 'acess_qr_text')->textInput() ?>
	</div>
	
    <div class="col-md-12">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
