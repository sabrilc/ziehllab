<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Laboratorista */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
<div class="row">
    <div class="col col-md-6">
        <?= $form->field($model, 'identificacion')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col col-md-6">
        <?= $form->field($model, 'nombres')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col col-md-6">
        <?= $form->field($model, 'cargo')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col col-md-6">
        <?= $form->field($model, 'registro_msp')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col col-md-6">
        <?= $form->field($model, 'registro_senescyt')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col col-md-6">
        <?= $form->field($model, 'responsable_tecnico')->checkbox(['style'=>'margin-top:30px;']) ?>
    </div>
    <div class="col col-md-12">
        <div class="row">
            <div class="col col-md-6">
                <?= $form->field($model, 'imageFile')->fileInput() ?>
            </div>
            <div class="col col-md-6">
               <?= $model->imageFirma()?>
            </div>
        </div>
    </div>


    <div class="col col-md-6">
        <?= $form->field($model, 'p12File')->fileInput() ?>
    </div>
    <div class="col col-md-6">
        <?= $model->imagenFirmaDigital()?>
        <?= $form->field($model, 'firma_digital_secret')->passwordInput() ?>
    </div>
    <div class="col col-md-12">
        <div class="form-group">
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>







</div>
<?php ActiveForm::end(); ?>
