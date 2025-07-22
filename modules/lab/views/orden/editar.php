<?php

use app\assets\ReactJsAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\lab\models\Orden */

ReactJsAsset::register($this);
$this->title = 'Editar Orden: ' . $model->codigo;
$this->params['breadcrumbs'][] = ['label' => 'Ordenes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->codigo, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editar';

?>

<div id="react-root"></div>

<!-- Incluir el archivo JSX con type="text/babel" -->
<script type="text/babel" src="<?= \yii\helpers\Url::to('@web/static/js/orden.nueva.jsx') ?>"></script>