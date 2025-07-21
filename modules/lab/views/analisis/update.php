<?php


/* @var $this yii\web\View */
/* @var $model app\models\Analisis */

$this->title = 'Editar Análisis: ' . $model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Análisis', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nombre, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="analisis-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>


   <?= $this->render('parametro_index', [
       'dataProvider' => $dataProvider,
       'GridModel' => $GridModel,
       'model' => $model,
    ]) ?>
  
<hr>    
    <?= $this->render('seccion_index', [
        'GridSeccion' => $GridSeccion,
        'dataProviderSeccion' => $dataProviderSeccion,
        'model' => $model,
        
    ]) ?>

</div>
