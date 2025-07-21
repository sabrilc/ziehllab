<?php

use app\assets\FontAwesomeIconsAsset;
use app\assets\OrdenNuevaPageAsset;
use app\assets\ReactJsAsset;
use app\assets\TypeaheadJSAsset;
use app\models\TipoAnalisis;
use app\models\User;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Laboratorista;
use app\models\Secretaria;

/* @var $this yii\web\View */
/* @var $model app\models\Orden */
FontAwesomeIconsAsset::register($this);
TypeaheadJSAsset::register($this);
ReactJsAsset::register($this);
OrdenNuevaPageAsset::register( $this);

$this->title = 'Nueva Orden';
$this->params['breadcrumbs'][] = ['label' => 'Ordenes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<div id="react-root"></div>

<!-- Incluir el archivo JSX con type="text/babel" -->
<script type="text/babel" src="<?= \yii\helpers\Url::to('@web/static/js/orden.nueva.jsx') ?>"></script>


