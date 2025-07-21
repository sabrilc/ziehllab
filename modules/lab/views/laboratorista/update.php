<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Laboratorista */

$this->title = 'Editar Laboratorista: ' . $model->nombres;
$this->params['breadcrumbs'][] = ['label' => 'Laboratoristas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nombres, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="laboratorista-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
