<?php


use app\assets\JSLoadingOverlayAsset;
use app\assets\OrdenPageAsset;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $GridModel app\models\OrdenGrid */
/* @var $dataProvider yii\data\ActiveDataProvider */

OrdenPageAsset::register($this);

$this->title = 'Ordenes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orden-index">


    <?php // echo $this->render('_Grid', ['model' => $GridModel]); ?>

    <p>
        <?php // Html::a('Nueva Orden versión anterior', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Nueva Orden', ['nueva'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $GridModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           // 'id',
            'codigo',
            
            [
                'attribute' => 'fecha',
                'value' => 'fecha',
                'filter' => \yii\jui\DatePicker::widget([
                    'model'=>$GridModel,
                    'attribute'=>'fecha',
                    'language' => 'es',
                    'dateFormat' => 'yyyy-MM-dd',

                ]),
                'format' => 'html',
            ],
            [
                'attribute' => 'paciente',
                'value' => 'paciente.nombresConCedula'
            ],
            ['attribute' => '_examenes',
                /*'filter' => ArrayHelper::map(Analisis::find()->asArray()->all(), 'id', 'nombre'),
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'options' => ['prompt' => 'Buscar','multiple'=>false],
                    'pluginOptions' => ['allowClear' => true],
                ],*/
                'label' => 'Análisis',
                'format'=>'raw',
                'value' => function ($model) {
                    $html="";
                    foreach ($model->examens as $examen) {
                        $html.=  "<li>". $examen->analisis->nombre." </li>";
                    }
                    return  $html;
                }
            ],
            [
                'attribute' => 'doctor',
                'value' => 'doctor.nombres'
            ],
            
            [
                'format'=>'raw',
                'attribute'=>'pagado',
                'filter'=>array("0"=>"NO PAGADO","1"=>"PAGADO"),
                'value'=> function ($model, $key, $index, $column){
                if($model->pagado==1){ return Html::tag('div',Html::button(Html::tag('i','',['class'=>'mdi mdi-check-decagram mr-2']).'PAGADA',['class'=>'btn btn-primary']));}
                else { return Html::tag('a',Html::button(Html::tag('i','',['class'=>'mdi mdi-alert-octagram mr-2']).'NO PAGADA',['class'=>'btn btn-danger']),['onclick'=>"pagar(". json_encode($model->attributes).")"]); }
                }
                
                ],
            
            [
                'format'=>'raw',
                'attribute'=>'cerrada',
                'label'=>'Finalizada',
                'filter'=>array("0"=>"NO","1"=>"SI"),
                'value'=> function ($model, $key, $index, $column){
                if($model->cerrada==1){
                    return Html::tag('div',
                        Html::button('SI',
                            ['class'=>'btn btn-primary']));}
                else { return Html::tag('div',
                    Html::button(
                            'NO',
                            ['class'=>'btn btn-danger'])); }
                }
                
            ],

            ['class' => 'yii\grid\ActionColumn','template' => '<div class="btn-group">{Imprimir}{Ticket}{EnProceso}{enviarMail}{Firmar}{Ver}{Editar}{Borrar}</div>',
                'contentOptions'=>[ 'style'=>'width: 250px'],
                'buttons' => [
				    'Ticket'=> function ($url,$model) {
                                         return Html::a(
                                                 Html::tag('div',
                                                     Html::tag('i','',
                                                         ['class'=>'mdi mdi-18px mdi-receipt']),
                                                            ['class'=>' btn btn-primary']
                                                 ) , \yii\helpers\Url::to(['orden/online-ticket','id'=>$model->id])
                                         ) ;
                                    },
                    'Imprimir'=> function ($url,$model) {
                                         return Html::a(
                                                 Html::tag('div',
                                                     Html::tag('i','',
                                                         ['class'=>'mdi mdi-18px mdi-printer']),
                                                            ['class'=>' btn btn-primary']
                                                 ) , \yii\helpers\Url::to(['orden/ver-resultado','id'=>$model->id])
                                         ) ;
                                    },

                    'EnProceso' => function ($url, $model) {
                    if( $model->cerrada == true) {
                    return  Html::a('<span class="mdi  mdi-18px mdi-refresh"></span>',
                        \yii\helpers\Url::to(['orden/poner-en-proceso','id' => $model->id]),
                        ['class'=>'btn btn-primary','title' => Yii::t('yii', 'Poner en proceso'),'aria-label' => Yii::t('yii', 'En Proceso')]);
                    }
                    },
                    'enviarMail' => function ($url, $model) {
                        return  Html::a('<i class="mdi  mdi-18px mdi-email"></i> ',
                            \yii\helpers\Url::to(['orden/enviar-mail','id' => $model->id]),
                            ['type'=>'button','class'=>'btn btn-primary','title' => Yii::t('yii', 'Enviar mail'),'aria-label' => Yii::t('yii', 'Enviar Mail')]);
             
                    },
                    'Editar' => function ($url, $model) {                        
                                   return  Html::a('<span> <b class="mdi  mdi-18px mdi-circle-edit-outline"></b></span> ',
                                                   \yii\helpers\Url::to(['orden/editar','id' => $model->id]),
                                                   ['class'=>'btn btn-primary','title' => Yii::t('yii', 'Editar'),'aria-label' => Yii::t('yii', 'Editar')]);
                         },
                         
                         'Ver' => function ($url, $model) {
                         
                         return Html::a('<span> <b class="mdi  mdi-18px mdi-eye-outline"></b></span> ',
                                     \yii\helpers\Url::to(['orden/view','id' => $model->id]),
                                     ['class'=>'btn btn-primary','title' => Yii::t('yii', 'Visualizar'),'aria-label' => Yii::t('yii', 'Visualizar')]);
                         },
                         'Borrar' => function ($url, $model) {

                         
                         return Html::a('<span> <b class="mdi mdi-18px mdi-trash-can-outline"></b></span> ',
                                         \yii\helpers\Url::to(['orden/delete','id' => $model->id]),
                                         ['class'=>'btn btn-danger','title' => Yii::t('yii', 'Borrar'),'aria-label' => Yii::t('yii', 'Borrar'),
                                             'data-confirm' => Yii::t('yii', 'Esta seguro de eliminar la orden:  '.$model->detalle.'?'),
                                             'data-method'  => 'post',
                                         ]).
                                         '</div>';
                         },


                               'Firmar' => function ($url, $model) {
                                             return Html::button('<i class="mdi mdi-18px mdi-fingerprint"></i> ',

                                                             ['class'=>'btn btn-primary',
                                                                 'title' => Yii::t('yii', 'Firmar'),
                                                                 'aria-label' => Yii::t('yii', 'Firmar'),
                                                                 'onclick'=>"firmar(". json_encode($model->attributes).")"
                                                             ]).
                                         '</div>';
                         }
                     ]
                            
               ]
        ],
    ]); ?>
</div>



