<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Parametro */

$this->title = $model->descripcion;
$this->params['breadcrumbs'][] = ['label' => 'Análisis', 'url' => ['/analisis/index']];
$this->params['breadcrumbs'][] = ['label' => $analisis->nombre, 'url' => ['/analisis/update','id'=>$analisis->id]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="parametro-view">

    <h1><?= Html::encode($analisis->nombre) ?></h1>
    <h2><?= Html::encode($this->title) ?></h2>

    <p>
        
        <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        
         <?= Html::a('Nuevo Parametro', ['parametro/create','analisis'=>$model->analisis_id], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'descripcion',
            'medida.descripcion:text:Medida', 
            'metodo.descripcion:text:Metodo',
            'seccion.descripcion:text:Seccion',
            'unico_valor_referencial',
            'hombre_valo_de_referencia_min',
            'hombre_valo_de_referencia_max',
            'mujer_valo_de_referencia_min',
            'mujer_valo_de_referencia_max',
            'ninio_valo_de_referencia_min',
            'ninio_valo_de_referencia_max',
            
            'valores_posibles',
           
                       
        ],
    ]) ?>

</div>
