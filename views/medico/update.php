<?php


/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'Editar el Médico: ' . $model->nombres;
$this->params['breadcrumbs'][] = ['label' => 'Médicos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nombres , 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="user-update">  

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
