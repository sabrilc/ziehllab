<?php

use app\models\TipoAnalisis;
use app\models\User;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Laboratorista;

/* @var $this yii\web\View */
/* @var $model app\models\Orden */

$this->title = 'Nueva Orden';
$this->params['breadcrumbs'][] = ['label' => 'Ordenes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<h5><?= Html::encode($this->title) ?></h5>

    <?php $form = ActiveForm::begin(); ?>
<div class="orden-create">




<?= $form->field($model, 'paciente_id')->widget(Select2::class, [
    'options'=>['prompt'=>'Seleccionar ...'],
    'data' => ArrayHelper::map(
        User::find()
        ->innerJoin('auth_assignment','user.id=auth_assignment.user_id')
        ->where(['item_name'=>'cliente'])
        ->all(), 'id', 'nombreCompleto'),
   
    
     ]) ?>


 <?= $form->field($model, 'doctor_id')->widget(Select2::class, [
     'options'=>['prompt'=>'Seleccionar ...'],
     'data' =>  ArrayHelper::map(
         User::find()
         ->innerJoin('auth_assignment','user.id=auth_assignment.user_id')
         ->where(['item_name'=>'medico'])
         ->all(), 'id', 'nombreCompleto'),
     
    
     ]) ?>  
     
  <?= $form->field($model, 'laboratorista_id')->widget(Select2::class, [
     'options'=>['prompt'=>'Seleccionar ...'],
     'data' =>  ArrayHelper::map(
         Laboratorista::find()
                             ->where(['dbremove'=>false])
                             ->all(), 'id', 'nombres'),
     
    
     ]) ?>  
   
    
 
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
    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>
 </div>
 <?php ActiveForm::end(); ?> 
