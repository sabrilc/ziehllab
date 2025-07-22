<?php

use yii\grid\GridView;
use yii\helpers\Html;


?>

  <p>
        <?= Html::a('Nuevo Parámetro', ['parametro/create','analisis'=>$model->id], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $GridModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'descripcion',
            'orden_impresion',
            'medida.descripcion:text:Unidad de Medida',
            'seccion.descripcion:text:Sección',
            
            //'hombre_valo_de_referencia_min',
            //'hombre_valo_de_referencia_max',
            //'mujer_valo_de_referencia_max',
            //'mujer_valo_de_referencia_min',
            //'ninio_valo_de_referencia_max',
            //'ninio_valo_de_referencia_min',
            //'valores_posibles',
            //'metodo_id',
            //'medida_id',
            //'analisis_id',
            //'created_at',
            //'updated_at',
            //'created_by',
            //'updated_by',

            ['class' => 'yii\grid\ActionColumn','template' => '{Editar}',
                'buttons' => [
                    'Editar' => function ($url, $model) {
                    
                    return '<div class="btn-group" role="group">'.
                        Html::a('<span> <b class="mdi mdi-circle-edit-outline"></b></span> ',
                            \yii\helpers\Url::to(['parametro/update','id' => $model->id]),
                            ['class'=>'btn btn-primary','title' => Yii::t('yii', 'Editar'),'aria-label' => Yii::t('yii', 'Editar')]).
                            
                            Html::a('<span> <b class="mdi mdi-eye-outline"></b></span> ',
                                \yii\helpers\Url::to(['parametro/view','id' => $model->id]),
                                ['class'=>'btn btn-primary','title' => Yii::t('yii', 'Visualizar'),'aria-label' => Yii::t('yii', 'Visualizar')]).
                                
                                Html::a('<span> <b class="mdi mdi-trash-can-outline"></b></span> ',
                                    \yii\helpers\Url::to(['parametro/delete','id' => $model->id]),
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