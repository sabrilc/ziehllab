<?php


use app\assets\OrdenPageAsset;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrdenSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

OrdenPageAsset::register($this);

$this->title = 'Ordenes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orden-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php // Html::a('Nueva Orden versión anterior', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Nueva Orden', ['nueva'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           // 'id',
            'codigo',
            
            [
                'attribute' => 'fecha',
               
                'value' => 'fecha',
                'filter' => \yii\jui\DatePicker::widget([
                    'model'=>$searchModel,
                    'attribute'=>'fecha',
                    'language' => 'es',
                   
                    'dateFormat' => 'yyyy-MM-dd',
                ]),
                'format' => 'html',
            ],
           // 'precio',
           // 'abono',
            //'pagado',
            [
                'attribute' => 'paciente',
                'value' => 'paciente.nombres'
            ],
            [
                'attribute' => 'doctor',
                'value' => 'doctor.nombres'
            ],
            
            [
                'format'=>'raw',
                'attribute'=>'pagado',
                'filter'=>array("0"=>"NO PAGADA","1"=>"PAGADA"),
                'value'=> function ($model, $key, $index, $column){
                if($model->pagado==1){ return Html::tag('div',Html::button(Html::tag('i','',['class'=>'mdi mdi-check-decagram mr-2']).'PAGADA',['class'=>'btn btn-success']));}
                else { return Html::tag('a',Html::button(Html::tag('i','',['class'=>'mdi mdi-alert-octagram mr-2']).'NO PAGADA',['class'=>'btn btn-warning']),['onclick'=>"pagar(". json_encode($model->attributes).")"]); }
                }
                
                ],
            
            [
                'format'=>'raw',
                'attribute'=>'cerrada',
                'filter'=>array("0"=>"EN PROCESO","1"=>"FINALIZADA"),
                'value'=> function ($model, $key, $index, $column){
                if($model->cerrada==1){ return Html::tag('div',Html::button(Html::tag('i','',['class'=>'mdi mdi-check-decagram mr-2']).'FINALIZADA',['class'=>'btn btn-success']));}
                else { return Html::tag('div',Html::button(Html::tag('i','',['class'=>'mdi mdi-alert-octagram mr-2']).'EN PROCESO',['class'=>'btn btn-warning'])); }
                }
                
            ],
            
            [
                'format'=>'raw',
                'attribute'=>'resultado',                
                'value'=> function ($model, $key, $index, $column){
                if($model->cerrada==true){
                    return Html::a(Html::tag('div', Html::tag('i','',['class'=>'mdi mdi-printer mr-2']).'IMPRIMIR RESULTADOS',['class'=>' btn btn-success']) , \yii\helpers\Url::to(['/orden/ver-resultado','id'=>$model->id])) ;
                }
                
                return Html::tag('div', Html::tag('i','',['class'=>'mdi mdi-printer-off mr-2']).'IMPRIMIR RESULTADOS',['class'=>' btn btn-danger']) ;
                
                }
                ],
            //'paciente_id',
            //'doctor_id',
            //'cotizacion_id',
            //'created_at',
            //'updated_at',
            //'created_by',
            //'updated_by',

                      
            ['class' => 'yii\grid\ActionColumn','template' => '{EnProceso}{enviarMail}{Ver}{Editar}{Borrar}',
                'buttons' => [
                    'EnProceso' => function ($url, $model) {
                    if( $model->cerrada == true) {
                    return  Html::a('<span> <b class="mdi mdi-refresh"></b></span> ',
                        \yii\helpers\Url::to(['orden/poner-en-proceso','id' => $model->id]),
                        ['class'=>'btn btn-warning','title' => Yii::t('yii', 'Poner en proceso'),'aria-label' => Yii::t('yii', 'En Proceso')]);
                    }
                    },
                    'enviarMail' => function ($url, $model) {
                        return  Html::a('<span> <b class="mdi mdi-email"></b></span> ',
                            \yii\helpers\Url::to(['orden/enviar-mail','id' => $model->id]),
                            ['class'=>'btn btn-success','title' => Yii::t('yii', 'Enviar mail'),'aria-label' => Yii::t('yii', 'Enviar Mail')]);
             
                    },
                    'Editar' => function ($url, $model) {                        
                                   return  Html::a('<span> <b class="mdi mdi-circle-edit-outline"></b></span> ',
                                                   \yii\helpers\Url::to(['orden/update','id' => $model->id]),
                                                   ['class'=>'btn btn-primary','title' => Yii::t('yii', 'Editar'),'aria-label' => Yii::t('yii', 'Editar')]);
                         },
                         
                         'Ver' => function ($url, $model) {
                         
                         return Html::a('<span> <b class="mdi mdi-eye-outline"></b></span> ',
                                     \yii\helpers\Url::to(['orden/view','id' => $model->id]),
                                     ['class'=>'btn btn-primary','title' => Yii::t('yii', 'Visualizar'),'aria-label' => Yii::t('yii', 'Visualizar')]);
                         },
                         'Borrar' => function ($url, $model) {
                         
                         return Html::a('<span> <b class="mdi mdi-trash-can-outline"></b></span> ',
                                         \yii\helpers\Url::to(['orden/delete','id' => $model->id]),
                                         ['class'=>'btn btn-danger','title' => Yii::t('yii', 'Borrar'),'aria-label' => Yii::t('yii', 'Borrar'),
                                             'data-confirm' => Yii::t('yii', 'Esta seguro de eliminar la orden ?'),
                                             'data-method'  => 'post',
                                         ]).
                                         '</div>';
                         }
                         
                     ]
                            
               ]
        ],
    ]); ?>
</div>



