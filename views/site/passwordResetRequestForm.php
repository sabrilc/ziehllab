<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Recuperar acceso';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container mt-5">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>Por favor ingrese el correo electronico. Un mensaje se enviara con los pasos para crear nueva clave.</p>
    <div class="row">
        <div class="col-lg-5">
 
            <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>
                <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>
                <div class="form-group">
                    <?= Html::submitButton('Enviar', ['class' => 'btn btn-primary']) ?>
                </div>
            <?php ActiveForm::end(); ?>
 
        </div>
    </div>
</div>
