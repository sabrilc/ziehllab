<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Orden */

$this->title = 'Descarga';
$this->params['breadcrumbs'][] = ['label' => 'Ordenes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<h1>Selecciones las fechas para generar la descarga </h1>

    <?= Html::beginForm(); ?>
<div class="orden-create">

  <div class="form-group">
    <label> Fecha de Inicio</label>
    <?= yii\jui\DatePicker::widget(['name' => 'fecha_inicio', 'dateFormat' => 'yyyy-MM-dd']) ?>
   
  </div>
  <div class="form-group">
    <label>Fecha de Fin</label>
    <?= yii\jui\DatePicker::widget(['name' => 'fecha_fin','dateFormat' => 'yyyy-MM-dd']) ?>
  </div>
  





    <div class="form-group">
        <?= Html::submitButton('Generar', ['class' => 'btn btn-success']) ?>
    </div>

</div>

 <?= Html::endForm(); ?> 
