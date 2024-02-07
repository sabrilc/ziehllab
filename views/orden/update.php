<?php

use app\models\Examen;
use app\models\Laboratorista;
use app\models\Secretaria;
use app\models\TipoAnalisis;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Analisis;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Orden */

$this->title = 'Editar Orden: ' . $model->codigo;
$this->params['breadcrumbs'][] = ['label' => 'Ordenes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->codigo, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="orden-update">
     <?php $form = ActiveForm::begin(); ?>
<div class="orden-create">

<?= $form->field($model, 'paciente_id')->widget( Select2::class, [
    'options'=>['placeholder'=>'Buscar ...'],
    'data' => ArrayHelper::map(
        User::find()
        ->innerJoin('auth_assignment','user.id=auth_assignment.user_id')
        ->where(['item_name'=>'cliente'])
        ->all(), 'id', 'nombreCompleto'),   
    
     ]) ?>
     
     <?= $form->field($model, 'doctor_id')->widget( Select2::class, [
     'options'=>['placeholder'=>'Buscar ...'],
	 'pluginOptions' => [
        'allowClear' => true
    ],
     'data' =>  ArrayHelper::map(
         User::find()
         ->innerJoin('auth_assignment','user.id=auth_assignment.user_id')
         ->where(['item_name'=>'medico'])
         ->all(), 'id', 'nombreCompleto'),
     ]) ?>  
     
       <?= $form->field($model, 'laboratorista_id')->widget( Select2::class, [
     'options'=>['placeholder'=>'Buscar ...'],
     'data' =>  ArrayHelper::map(
         Laboratorista::find()
                             ->where(['dbremove'=>false])
                             ->all(), 'id', 'nombres'),
     ]) ?>

    <?= $form->field($model, 'responsable_tecnico_id')->widget( Select2::class, [
        'options'=>['placeholder'=>'Buscar ...'],
        'data' =>  ArrayHelper::map(
            Laboratorista::find()
                ->where(['dbremove'=>false])
                ->andWhere(['responsable_tecnico'=>1])
                ->all(), 'id', 'nombres'),
    ]) ?>


    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

</div>
<div class="row">
<?php foreach (TipoAnalisis::find()->All() as $categoria) {  ?>  
							<div class="col-md-3">
								<div class="card" style="width: 18rem;">									
									<div class="card-body">
										<h6 class="btn btn-primary"> <?php echo $categoria->descripcion;?></h6>
									
										<?php 	echo Html::checkboxList('analisis',
										            ArrayHelper::map(
										                Examen::find()
										                ->joinWith(['analisis'])
										                ->where( ['analisis.tipo_analisis_id'=>$categoria->id,'orden_id'=>$model->id ])
										                ->all(), 'analisis_id','analisis_id'), 
										    
												    ArrayHelper::map(
												        Analisis::find()
												        ->where(['analisis.tipo_analisis_id'=>$categoria->id])->all(), 'id', 'nombreCosto'),
												    ['separator'=>'<br/>', 'itemOptions'=>['class'=>'filled-in'],'labelOptions'=>['class'=>'container']]); 
												?>
												
										
									</div>
								</div>
							</div>    
 <?php } ?>
	</div>
 <?php ActiveForm::end(); ?> 

</div>
