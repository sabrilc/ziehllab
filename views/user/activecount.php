 	<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->primer_apellido. ' '.$model->segundo_apellido .' '.$model->nombres;
//$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
    <?php if(!empty($msg)){ ?>
                    <div class="alert alert-info">
                        <strong><?= $msg ?></strong>
                    </div>
                    <?php }?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'username:text:Usuario',
            'email:email',
           // 'auth_key',
            //'created_at',
            //'updated_at',
            //'password',
            //'_password',
            'nombres',
            'primer_apellido',
            'segundo_apellido',
            'identificacion',
            //'foto',
            'estado.descripcion:text:Estado',
        ],
    ]) ?>
    <div class="pad-top">
                        <?= Html::a(Yii::t('app', '<i class="m-icon-swapleft m-icon-white"></i> Ingresar'), ['site/login'], ['class' => 'btn grey-mint pull-right']) ?>
                    </div>

</div>
