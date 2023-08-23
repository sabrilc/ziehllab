<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Germen */

$this->title = 'Nuevo Germen';
$this->params['breadcrumbs'][] = ['label' => 'Germenes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="germen-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
