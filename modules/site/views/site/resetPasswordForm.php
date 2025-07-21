<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Recuperar acceso';
$this->params['breadcrumbs'][] = $this->title;
?>
 
<div class="container mt-5">
 <div class="row">
   
   
        <div class="col-lg-5">
		 <h1><?= Html::encode($this->title) ?></h1>
		<p>Escriba su nueva clave:</p>
 
            <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>
                <?= $form->field($model, 'password')->passwordInput(['autofocus' => true]) ?>
                <div class="form-group">
                    <?= Html::submitButton('Enviar', ['class' => 'btn btn-primary']) ?>
                </div>
            <?php ActiveForm::end(); ?>
 
        </div>
    </div>
</div>