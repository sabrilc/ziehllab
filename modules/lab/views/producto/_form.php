<?php

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Periodo;

/* @var $this yii\web\View */
/* @var $model app\models\Producto */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="producto-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($model, 'id_periodo')->widget(Select2::class,
        [
    'data' =>ArrayHelper::map(Periodo::find()->where(['activo'=>true])->all(), 'id', 'descripcion'),
        'options' => ['placeholder' => 'Seleccionar ...'],
        'pluginOptions' => [
        'allowClear' => true
    ],
        ]);?>

    <?= $form->field($model, 'descripcion')->textInput(['maxlength' => true]) ?>


    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
