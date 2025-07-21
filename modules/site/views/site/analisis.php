<?php

use app\models\TipoAnalisis;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $GridModel app\models\CotizacionGrid */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Analisis';
$this->params['breadcrumbs'][] = $this->title;

?>




        <div class="container">          
                <div class="row">  
                    <?php foreach (TipoAnalisis::find()->All() as $categoria) {  ?>    
                     <div class="col-lg-3 col-md-3">                        
                        <div class="card  m-2" style="width: 18rem;">                          
                          <div class="card-body">
                            <h5 class="card-title btn btn-primary"><?php echo $categoria->descripcion;?></h5>
                            <p class="card-text">
                            <?php   echo Html::checkboxList('analisis','', ArrayHelper::map($categoria->analises, 'id', 'nombre'),
                         					    [   'separator'=>'<br>',
                         					        
                         					        'itemOptions'=>[  
                         					           
                         					            'labelOptions' => [ ],
                         					            
                         					        ]
                         					        
                         					        ]); ?>
                            </p>
                           
                          </div>
                        </div>
                        </div>  
                        
                                    
                     <?php } ?>

                         
                </div>	
        </div>

 
 

 	

		

