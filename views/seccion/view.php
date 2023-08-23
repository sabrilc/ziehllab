<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Seccion */

$this->title = $model->descripcion;
$this->params['breadcrumbs'][] = ['label' => 'Análisis', 'url' => ['/analisis/index']];
$this->params['breadcrumbs'][] = ['label' => $analisis->nombre, 'url' => ['/analisis/update','id'=>$analisis->id]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="seccion-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'descripcion',
            'analisis.nombre:text:Analisis',
        ],
    ]) ?>

</div>
