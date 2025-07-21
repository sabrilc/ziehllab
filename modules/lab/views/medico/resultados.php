<?php

use luya\bootstrap4\grid\GridView;
use app\assets\MedicoAsset;

/* @var $this yii\web\View */

/* @var $dataProvider yii\data\ActiveDataProvider */

MedicoAsset::register($this);
$this->title = 'Análisis de pacientes';
$this->params['breadcrumbs'][] = $this->title;
?>

<?= GridView::widget(
    [
    'dataProvider' => $dataProvider,
    'filterModel' => $GridModel,
        'options' => [
            'class' => 'table-responsive',
        ],
    'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'codigo:Text:Orden',
                   
                    ['class' => 'yii\grid\ActionColumn','template' => '{Editar}',
                        'buttons' => [
                                        'Editar' => function ($url, $model) {
                                                    $html="";                            
                                                    foreach ($model->examens as $examen) {
                                                                $html.=  "<li>". $examen->analisis->nombre." </li>";
                                                            }
                                                     $html .=' <a href="#" class="btn btn-sm btn-primary mt-2" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="verOrden(\''.base64_encode($model->id).'\')">
																		<i class="mdi mdi-printer"> </i> Imprimir
															</a>';
													return  $html;
                                        }
                                       ]
                            
                     ],
                     ['attribute'=>'fecha',
                         'format' => 'raw',
                         'value' => function ($model) {
                         return '<p class="badge badge-success"> '.$model->fecha.'</p>';
                         }
                         ],
                                        
                    [
                        'attribute' => 'paciente',
                        'value' => 'paciente.nombres'
                    ],
                    // 'valor_total',
                    /* ['class' => 'yii\grid\ActionColumn','template' => '{Editar}',
                         'buttons' => [
                                     'Editar' => function ($url, $model) {
                                    
                                     return  '<a href="#" class="btn btn-sm btn-info mt-2" data-toggle="modal" data-target=".bd-example-modal-lg" onclick="verOrden('.$model->id.')">
                                     <i class="mdi mdi-pdf"> </i> Ver
                                     </a>';
                                     }
                             ]
                             
                             ],*/
       
                 ],
     ]);


?>

<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div id="modal_content" class="modal-content">
      
    </div>
  </div>
</div>



