<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Seccion */

$this->title =  $model->descripcion;
$this->params['breadcrumbs'][] = ['label' => 'Análisis', 'url' => ['/analisis/index']];
$this->params['breadcrumbs'][] = ['label' => $analisis->nombre, 'url' => ['/analisis/update','id'=>$analisis->id]];
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="seccion-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
