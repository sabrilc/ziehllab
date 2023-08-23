<?php
use app\models\Sexo;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
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
     <?= $form->field($model, 'sexo_id')->dropDownList(
         ArrayHelper::map(Sexo::find()->orderBy('id')->all(), 'id', 'descripcion'),
         ['prompt' => 'Seleccionar ...'])
     ;?>
</div>
<div class="col-md-6">
 <?= $form->field($model, 'edad')->textInput(['maxlength' => true]) ?>  
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
