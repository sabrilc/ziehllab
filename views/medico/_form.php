<?php

use app\models\Sexo;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>
 <?php $form = ActiveForm::begin(); ?>
<div class="row">

<div class="col-md-2"> 
   <?= $form->field($model, 'identificacion')->textInput(['maxlength' => true]) ?>
   </div>

<div class="col-md-10"> <?= $form->field($model, 'nombres')->textInput(['maxlength' => true]) ?>   </div>
<div class="col-md-6">
     <?= $form->field($model, 'sexo_id')->dropDownList(
         ArrayHelper::map(Sexo::find()->orderBy('id')->all(), 'id', 'descripcion'),
         ['prompt' => 'Seleccionar ...'])
     ;?>
</div>
<div class="col-md-6">
 <?= $form->field($model, 'edad')->textInput(['maxlength' => true]) ?>  
</div>
<div class="col-md-12">
<div class="card primary">
<div class="card-header" >
<h5>Datos para el acceso al sistema</h5>
</div>
<div class="card-body row">
<div class="col-md-6"><?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?></div>
<div class="col-md-6"> <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?></div>
<div class="col-md-6"><?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?></div>
<div class="col-md-6">  <?= $form->field($model, '_password')->passwordInput(['maxlength' => true]) ?></div>
<div class="col-md-2"> <?= $form->field($model, 'activo')->checkbox() ?></div>
</div>
</div>


    <div class="col-md-6 mt-2">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

   

</div>
</div>

 <?php ActiveForm::end(); ?>
