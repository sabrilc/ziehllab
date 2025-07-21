<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Analisis */

$this->title = $model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Análisis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="analisis-view">

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
           // 'id',
            'codigo',
            'nombre',
            'descripcion',
            'precio',            
            'tipoMuestra.descripcion:text:Tipo de Muestra',
            'tipoAnalisis.descripcion:text:Tipo de Análisis',
            'activo',
           // 'created_at',
            //'updated_at',
            //'created_by',
            //'updated_by',
        ],
    ]) ?>

</div>
