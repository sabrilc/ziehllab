<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Orden */

$this->title = 'Informe';
$this->params['breadcrumbs'][] = ['label' => 'Ordenes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<h4>Informe Operativo</h4>
<p>Selecciones las fechas para generar el Informe </p>
    <?= Html::beginForm(); ?>
<div class="orden-create">
<div class="row">
<div class="col-md-3">
  <div class="form-group">
    <label> Fecha de Inicio</label>
    <?= yii\jui\DatePicker::widget(['name' => 'fecha_inicio', 'dateFormat' => 'yyyy-MM-dd', 'options' => ['placeholder' => 'Seleccione..','autocomplete' => 'off'],]) ?>
   
  </div>
  </div>
  <div class="col-md-3">
  <div class="form-group">
    <label>Fecha de Fin</label>
    <?= yii\jui\DatePicker::widget(['name' => 'fecha_fin','dateFormat' => 'yyyy-MM-dd','options' => ['placeholder' => 'Seleccione..','autocomplete' => 'off']]) ?>
  </div>
  </div>
  




<div class="col-md-6">
    <div class="form-group">
        <?= Html::submitButton('Generar', ['class' => 'btn btn-success']) ?>
    </div>
</div>
</div>
</div>

 <?= Html::endForm(); ?> 
