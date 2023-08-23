<?php

use luya\bootstrap4\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Laboratorista */

$this->title = 'Consulta de Resultados';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class='containe'>
    <div class="row">
        <div class="column-12">
        
            <h1><?= Html::encode($this->title) ?></h1>
        
              <div class="laboratorista-form">
            
                <?php $form = ActiveForm::begin(); ?>
            
                <?= $form->field($model, 'codigo')->textInput(['maxlength' => true]) ?>
            
                <?= $form->field($model, 'codigo_secreto')->textInput(['maxlength' => true]) ?>
            
                
               
                <div class="form-group">
                    <?= Html::submitButton('Descargar', ['class' => 'btn btn-success']) ?>
                </div>
            
                <?php ActiveForm::end(); ?>
            
            </div>
        
        </div>
    </div>
</div>

