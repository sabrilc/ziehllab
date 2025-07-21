<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ParametroGrid */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="parametro-Grid">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'descripcion') ?>

    <?= $form->field($model, 'hombre_valo_de_referencia_min') ?>

    <?= $form->field($model, 'hombre_valo_de_referencia_max') ?>

    <?= $form->field($model, 'mujer_valo_de_referencia_max') ?>

    <?php // echo $form->field($model, 'mujer_valo_de_referencia_min') ?>

    <?php // echo $form->field($model, 'ninio_valo_de_referencia_max') ?>

    <?php // echo $form->field($model, 'ninio_valo_de_referencia_min') ?>

    <?php // echo $form->field($model, 'valores_posibles') ?>

    <?php // echo $form->field($model, 'metodo_id') ?>

    <?php // echo $form->field($model, 'medida_id') ?>

    <?php // echo $form->field($model, 'analisis_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Grid', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
