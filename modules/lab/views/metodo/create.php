<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Metodo */

$this->title = 'Nuevo Metodo';
$this->params['breadcrumbs'][] = ['label' => 'Metodos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="metodo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
