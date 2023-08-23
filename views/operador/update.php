<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'Editar el Operador: ' . $model->nombres;
$this->params['breadcrumbs'][] = ['label' => 'Operadores', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nombres , 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
