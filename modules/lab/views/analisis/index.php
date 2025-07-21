<?php

use yii\helpers\Html;
use luya\bootstrap4\grid\GridView;

/* @var $this yii\web\View */
/* @var $GridModel app\models\AnalisisGrid */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Análisis';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="analisis-index">

    <p>
        <?= Html::a('Nuevo Análisis', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $GridModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           // 'id',
            'codigo',
            'nombre',
            'hoja_impresion',
            'orden_impresion',
            'precio',
            ['attribute'=>'activo',
                'format' => 'raw',
                'filter'    => [ true=>"SI", false=>"NO" ],
                'value' => function ($model) {
                if($model->activo){ return '<p class="badge badge-primary"> SI </p>';}
                else{ return '<p class="badge badge-secondary"> NO </p>';}
                    
                }
                ],
        
            //'tipo_muestra_id',
            //'tipo_analisis_id',
            //'created_at',
            //'updated_at',
            //'created_by',
            //'updated_by',

            ['class' => 'yii\grid\ActionColumn','template' => '{Editar}',
                'buttons' => [
                    'Editar' => function ($url, $model) {
                    
                    return '<div class="btn-group" role="group">'.
                        Html::a('<span> <b class="mdi mdi-circle-edit-outline"></b></span> ',
                            \yii\helpers\Url::to(['analisis/update','id' => $model->id]),
                            ['class'=>'btn btn-primary','title' => Yii::t('yii', 'Editar'),'aria-label' => Yii::t('yii', 'Editar')]).
                            
                            Html::a('<span> <b class="mdi mdi-eye-outline"></b></span> ',
                                \yii\helpers\Url::to(['analisis/view','id' => $model->id]),
                                ['class'=>'btn btn-primary','title' => Yii::t('yii', 'Visualizar'),'aria-label' => Yii::t('yii', 'Visualizar')]).
                                
                                Html::a('<span> <b class="mdi mdi-trash-can-outline"></b></span> ',
                                    \yii\helpers\Url::to(['analisis/delete','id' => $model->id]),
                                    ['class'=>'btn btn-primary','title' => Yii::t('yii', 'Borrar'),'aria-label' => Yii::t('yii', 'Borrar'),
                                        'data-confirm' => Yii::t('yii', 'Esta seguro de eliminar el analisis ?'),
                                        'data-method'  => 'post',
                                    ]).
                                    '</div>';
                    }
                    ]
                    
                    ]
        ],
    ]); ?>
</div>
