<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Laboratorista */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="laboratorista-form">

    <?php $form = ActiveForm::begin(); ?>
	
	   <?= $form->field($model, 'identificacion')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nombres')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cargo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'registro_msp')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'registro_senescyt')->textInput(['maxlength' => true]) ?>

   
    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
