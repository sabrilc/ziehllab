<?php

use app\models\TipoAnalisis;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $GridModel app\models\CotizacionGrid */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Nueva Cotizacion';
$this->params['breadcrumbs'][] = $this->title;

?>




        <div class="container">
          <?php $form= ActiveForm::begin(['method'=>'post','options'=> [ 'id'=>"signup-form", 'class' => "signup-form"]]); ?>
          
           <h2> Solicitar cotizaci&oacute;n de examenes clinicos </h2>  
            <div class="form-group">                   
                         <p class="desc">Ingrese un correo electr&oacute;nico valido, se le enviara la cotizacion por este medio!.</p>                   
                        <div class="fieldset-content">                        
                              
                                  <?= $form->field($model, 'nombres')->textInput(['maxlength' => true,]) ?>                                
                                  <?= $form->field($model, 'apellidos')->textInput(['maxlength' => true]) ?>                          
				     
                            <div class="form-group">
                                 <?= $form->field($model, 'email')->textInput(['type' => 'email']) ?>
                                <span class="text-input">Ejemplo  :<span class="badge badge-secondary">  Jeff@gmail.com</span></span>
                            </div>
                            <div class="form-group">
                                <?= $form->field($model, 'telefono')->textInput(['maxlength' => true]) ?>
                            </div>                           
                            
                        </div>
          
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

             <div class="col-md-12 text-center">
                
                     <button  class="btn btn-success" type="submit"> Enviar Cotizacion </button>
                 
                </div>
             
             
                </div>
                
           <?php ActiveForm::end();?>	
        </div>
        </div>


        <?php 
$this->registerJs("
if('$mensaje' != ''){
 swal('COTIZACION RESLIZADA CON EXITO!.', '$mensaje' ,'success');
}


 ",View::POS_END);
    
?>

 
 

 	

		

