<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductoGrid */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="producto-Grid">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'descripcion') ?>

    <?= $form->field($model, 'id_periodo') ?>

    <?= $form->field($model, 'gasto') ?>

    <?= $form->field($model, 'ingreso') ?>

    <?php // echo $form->field($model, 'ganancia') ?>

    <?php // echo $form->field($model, 'valor_control') ?>

    <div class="form-group">
        <?= Html::submitButton('Grid', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
