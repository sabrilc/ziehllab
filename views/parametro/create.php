<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Parametro */

$this->title = 'Nuevo Parametro';
$this->params['breadcrumbs'][] = ['label' => 'Análisis', 'url' => ['/analisis/index']];
$this->params['breadcrumbs'][] = ['label' => $analisis->nombre, 'url' => ['/analisis/update','id'=>$analisis->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="parametro-create">
 <h1><?= Html::encode($analisis->nombre) ?></h1>
    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
