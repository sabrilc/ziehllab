<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Antibiotico */

$this->title = 'Nuevo Antibiotico';
$this->params['breadcrumbs'][] = ['label' => 'Antibioticos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="antibiotico-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
