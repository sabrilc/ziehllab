<?php


/* @var $this yii\web\View */
/* @var $model app\models\Cotizacion */

$this->title = 'Editar Cotizacion: ' . $model->codigo;
$this->params['breadcrumbs'][] = ['label' => 'Cotizaciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->codigo, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="cotizacion-update">

    <?= $this->render('_formEdit', [
        'model' => $model,
    ]) ?>

</div>
