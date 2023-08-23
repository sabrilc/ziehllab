<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Medida */

$this->title = 'Nueva  Medida';
$this->params['breadcrumbs'][] = ['label' => 'Medidas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="medida-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
