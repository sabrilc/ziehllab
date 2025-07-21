<?php

use yii\grid\GridView;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $GridModel app\models\CotizacionGrid */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cotizaciones';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cotizacion-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_Grid', ['model' => $GridModel]); ?>

    <p>
        <?= Html::a('Nueva Cotizacion', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $GridModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'codigo',
            'nombres',
            'apellidos',
            'email:email',
            //'telefono',
            'total',
            //'vigente',
            //'fecha',
            //'vista',
            //'orden_id',
            //'created_at',
            //'updated_at',
            //'created_by',
            //'updated_by',

            ['class' => 'yii\grid\ActionColumn','template' => '{Editar}',
                'buttons' => [
                    'Editar' => function ($url, $model) {
                    
                    return '<div class="btn-group" role="group">'.
                        Html::a('<span> <b class="mdi mdi-circle-edit-outline"></b></span> ',
                            \yii\helpers\Url::to(['cotizacion/update','id' => $model->id]),
                            ['class'=>'btn btn-primary','title' => Yii::t('yii', 'Editar'),'aria-label' => Yii::t('yii', 'Editar')]).
                            
                            Html::a('<span> <b class="mdi mdi-eye-outline"></b></span> ',
                                \yii\helpers\Url::to(['cotizacion/view','id' => $model->id]),
                                ['class'=>'btn btn-primary','title' => Yii::t('yii', 'Visualizar'),'aria-label' => Yii::t('yii', 'Visualizar')]).
                                
                                Html::a('<span> <b class="mdi mdi-trash-can-outline"></b></span> ',
                                    \yii\helpers\Url::to(['cotizacion/delete','id' => $model->id]),
                                    ['class'=>'btn btn-primary','title' => Yii::t('yii', 'Borrar'),'aria-label' => Yii::t('yii', 'Borrar'),
                                        'data-confirm' => Yii::t('yii', 'Esta seguro de borrar la cotizacion ?'),
                                        'data-method'  => 'post',
                                    ]).
                                    '</div>';
                    }
                    ]
                    
                    ]
        ],
    ]); ?>
</div>
