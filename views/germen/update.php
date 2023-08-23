<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Germen */

$this->title = 'Editar Germen: ' . $model->descripcion;
$this->params['breadcrumbs'][] = ['label' => 'Germenes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->descripcion, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="germen-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
