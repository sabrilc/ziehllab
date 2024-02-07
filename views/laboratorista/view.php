<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Laboratorista */

$this->title = $model->nombres;
$this->params['breadcrumbs'][] = ['label' => 'Laboratoristas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="laboratorista-view">
    <p>
        <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Borrar', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Esta seguro de borra el laboratorista?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'identificacion',
            'nombres',
            'cargo',
            'registro_msp',
            'registro_senescyt',
            [   'attribute'=>"responsable_tecnico",
                'label'=>'Responsable Tecnico',
                'format'=>'raw',
                'value'=>function ($model) {
                     if($model->responsable_tecnico){ return Html::tag('div','SI',['class'=>'btn btn-primary']);}
                     else{return Html::tag('div','NO',['class'=>'btn btn-secondary']);}

                }
            ],
            [   'attribute'=>"firma_digital_fullname",
                'label'=>'Firma Digital',
                'format'=>'raw',
                'value'=>function ($model) {
                     return  $model->imagenFirmaDigital();
                }
            ],
            [   'attribute'=>"dir_imagen_firma",
                'label'=>'Firma Manual',
                'format'=>'raw',
                'value'=>function ($model) {
                    return  $model->imageFirma();
                }
            ]

        ],
    ]) ?>

</div>
