<?php

use app\assets\FontAwesomeIconsAsset;
use app\assets\OrdenNuevaPageAsset;
use app\assets\TypeaheadJSAsset;
use app\models\TipoAnalisis;
use app\models\User;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Laboratorista;
use app\models\Secretaria;

/* @var $this yii\web\View */
/* @var $model app\models\Orden */
FontAwesomeIconsAsset::register($this);
TypeaheadJSAsset::register($this);
OrdenNuevaPageAsset::register( $this);

$this->title = 'Nueva Orden';
$this->params['breadcrumbs'][] = ['label' => 'Ordenes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_add_cliente',['model'=>$model]) ?>


    <?php $form = ActiveForm::begin(); ?>
<div class="row">



<?= $form->field($model, 'paciente_id')->hiddenInput()->label(false) ?>

<div class="col-sm-12 col-md-6">
		 <?= $form->field($model, 'doctor_id')->widget(Select2::class, [
                         'options'=>['prompt'=>'Seleccionar ...'],
                         'data' =>  ArrayHelper::map(
                             User::find()->alias('u')
                             ->innerJoin('auth_assignment','u.id=auth_assignment.user_id')
                             ->where(['item_name'=>'medico'])
                             ->all(), 'id', 'nombreCompleto'),
                         
                        
                         ]) ?> 
</div>
 
   <div class="col-sm-12 col-md-6">
    <?= $form->field($model, 'laboratorista_id')->widget(Select2::class, [
                 'options'=>['prompt'=>'Seleccionar ...'],
                 'data' =>  ArrayHelper::map(
                     Laboratorista::find()
                                         ->where(['dbremove'=>false])
                                         ->all(), 'id', 'nombres'),
                 
                
                 ]) ?>  
   </div>

    <div class="col-sm-12 col-md-6">
        <?= $form->field($model, 'responsable_tecnico_id')->widget(Select2::class, [
            'options'=>['prompt'=>'Seleccionar ...'],
            'data' =>  ArrayHelper::map(
                Laboratorista::find()
                    ->where(['dbremove'=>false])
                    ->andWhere(['responsable_tecnico'=>1])
                    ->all(), 'id', 'nombres'),


        ]) ?>
    </div>




    <?= $form->field($model, 'cotizacion_id')->hiddenInput()->label(false) ?>




</div>
<div class="row">
<?php foreach (TipoAnalisis::find()->All() as $categoria) {  ?>   
<div class="col-md-3">		
    <div class="card" style="width: 18rem;">
			<div class="card-body"> 
			<h6 class="btn btn-primary"> <?php echo $categoria->descripcion;?></h6>
				<?php 	echo Html::checkboxList('analisis',
							                                '', 
							                                ArrayHelper::map($categoria->analises, 'id', 'nombreCosto'),
							    ['separator'=>'<br/>', 'itemOptions'=>['class'=>'filled-in'],'labelOptions'=>['class'=>'container']]); ?>
					
				</div>
			</div>
		</div>
							
    
 <?php } ?>
</div>
<div class="row text-center">
    <div style="position: fixed !important; bottom: 45px; right: 15px; display: inherit;">
        <?= Html::submitButton('Guardar',
            ['class' => 'btn btn-primary', 
             'style'=>'width: 120px !important; height: 40px !important; padding: 6px 0px; font-size: 18px; text-align: center;'                
            ]) ?>
    </div>
 </div>
 <?php ActiveForm::end(); ?> 
