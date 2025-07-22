<?php


use app\modules\lab\models\Sexo;
use app\modules\site\bussines\UserBussines;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\jui\JuiAsset;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model UserBussines */
/* @var $form yii\widgets\ActiveForm */

JuiAsset::register($this);
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
    <div class="form-group">
        <label> Fecha de Nacimiento*</label>
        <?= DatePicker::widget([
            'model' => $model,
            'attribute' => 'fecha_nacimiento',
            'options' => [
                'class' => 'form-control',
                'placeholder' => 'Seleccione...',
                'autocomplete' => 'off',
                'readonly' => false,
            ],
            'dateFormat' => 'dd-MM-yyyy',
        ]) ?>
    </div>


</div>


<div class="col-md-6"> <?= $form->field($model, 'email_notificacion')->textInput(['maxlength' => true]) ?></div>
<div class="col-md-12">
 <?= $form->field($model, 'direccion')->textarea(['rows'=>3])?>  
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
