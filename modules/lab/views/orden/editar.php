<?php

use app\modules\lab\grids\ClienteGrid;
use app\modules\lab\models\Examen;
use app\modules\lab\models\Laboratorista;
use app\modules\lab\models\TipoAnalisis;

use app\modules\site\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use app\modules\lab\models\Analisis;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\modules\lab\models\Orden */

$this->title = 'Editar Orden: ' . $model->codigo;
$this->params['breadcrumbs'][] = ['label' => 'Ordenes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->codigo, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editar';

$pacientes =[];
$pacienteSelected=null;
if($model->paciente_id){
    $pacientes = ArrayHelper::map( ClienteGrid::find()->where(['id'=>$model->paciente_id])->all(), 'id','nombreCompleto');
    $pacienteSelected=$model->paciente_id;
}
?>
<?php $form = ActiveForm::begin(); ?>


<?= $form->field($model, 'paciente_id')->widget(Select2::class, [
    'initValueText' => $pacientes,// array of text to show in the tag for the selected items
    'showToggleAll' => false,
    'options' => ['placeholder' => 'Buscar',
        //'tags' => true,
        'multiple' =>false,
        'value' => $pacienteSelected, // array of Id of the selected items
        'class' => 'validate'
    ],
    'pluginOptions' => [
        'tags' => true,
        'tokenSeparators' => [','],
        'allowClear' => false,
        'minimumInputLength' => 1,
        'language' => [
            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
        ],
        'ajax' => [
            'url' => \yii\helpers\Url::to(['orden/paciente-list']),
            'dataType' => 'json',
            'data' => new JsExpression('function(params) {return {q:params.term}; }')
        ],
        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
        'templateResult' => new JsExpression('function(data) {return data.text; }'),
        'templateSelection' => new JsExpression('function (data) {  return data.text; }'),
    ],
]);


?>


     <?= $form->field($model, 'doctor_id')->widget( Select2::class, [
     'options'=>['placeholder'=>'Buscar ...'],
	 'pluginOptions' => [
        'allowClear' => true
    ],
     'data' =>  ArrayHelper::map(
         User::find()->alias('u')
         ->innerJoin('auth_assignment','u.id=auth_assignment.user_id')
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

<div class="row text-center">
    <div style="position: fixed !important; bottom: 45px; right: 15px; display: inherit;">
        <?= Html::submitButton('Guardar',
            ['class' => 'btn btn-primary',
                'style'=>'width: 120px !important; height: 40px !important; padding: 6px 0px; font-size: 18px; text-align: center;'
            ]) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>