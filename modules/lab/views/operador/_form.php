<?php

use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'identificacion')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'nombres')->textInput(['maxlength' => true]) ?>
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

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
            
    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, '_password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'activo')->checkbox() ?>
    

   
    






  




    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
