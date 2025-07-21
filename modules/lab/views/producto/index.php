<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $GridModel app\models\ProductoGrid */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Productos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="producto-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_Grid', ['model' => $GridModel]); ?>

    <p>
        <?= Html::a('Nuevo Producto', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $GridModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'descripcion:text:Producto',
            'periodo.descripcion:text:Periodo',
           // 'gasto',
            //'ingreso',
            //'ganancia',
            //'valor_control',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
