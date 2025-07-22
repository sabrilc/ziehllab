<?php

use app\modules\lab\models\Analisis;
use app\modules\lab\models\CotizacionAnalisis;
use app\modules\lab\models\TipoAnalisis;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model Cotizacion */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cotizacion-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nombres')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'apellidos')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'telefono')->textInput(['maxlength' => true]) ?>

    <div class="row">
  <?php foreach (TipoAnalisis::find()->All() as $categoria) {  ?>
    				<div class="col-md-3"> 
							<div class="card">
									<div class="card-body">
										<h6 class="btn btn-primary"> <?php echo $categoria->descripcion;?></h6>																						    
												    	<?php 	echo Html::checkboxList('analisis',
										            ArrayHelper::map(
										                CotizacionAnalisis::find()
										                ->select(['analisis_id as id'])
										                ->joinWith(['analisis'])
										                ->where(
										                    ['analisis.tipo_analisis_id'=>$categoria->id,
										                     'cotizacion_id'=>$model->id
										                        
										                    ])->all(), 'id','id'), 
										    
												    ArrayHelper::map(
												        Analisis::find()->select('id, nombre')->where(['analisis.tipo_analisis_id'=>$categoria->id])->all(), 'id', 'nombre'),
												    ['separator'=>'<br/>', 'itemOptions'=>['class'=>'filled-in'],'labelOptions'=>['class'=>'container']]); 
												?>
								
									</div>
							</div>
						</div>
    
 <?php } ?>

 </div>


<div class="row text-center">
  <div class="col-md-12"> 
    <div class="form-group">
        <?= Html::submitButton('Cotizar', ['class' => 'btn btn-success']) ?>
    </div>
   </div>
   </div> 
    
    
    

    <?php ActiveForm::end(); ?>

</div>
