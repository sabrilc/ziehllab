<?php


/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'Cuenta';
$this->params['breadcrumbs'][] = $this->title
?>
<div class="user-update">
    <?= $this->render('_form_acount', [
        'model' => $model,
    ]) ?>

</div>
