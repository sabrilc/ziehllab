<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Parametro */

$this->title = $model->descripcion;
$this->params['breadcrumbs'][] = ['label' => 'Análisis', 'url' => ['/analisis/index']];
$this->params['breadcrumbs'][] = ['label' => $analisis->nombre, 'url' => ['/analisis/update','id'=>$analisis->id]];
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="parametro-update">

 
  <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
