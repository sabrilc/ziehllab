<?php


use app\modules\lab\models\Examen;
use app\modules\lab\models\TipoAnalisis;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;
use app\assets\OrdenPageAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\lab\models\Orden */

OrdenPageAsset::register($this);

$this->title = $model->codigo;
$this->params['breadcrumbs'][] = ['label' => 'Ordenes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orden-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Editar', ['editar', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Esta seguro de eliminar la orden?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
           // 'id',
            'codigo',
            'codigo_secreto',
            'fecha',
            'paciente.nombreCompleto:text:Paciente',
            'doctor.nombreCompleto:text:Doctor',
            'laboratorista.nombreCompleto:text:Laboratorista',
            'responsableTecnico.nombreCompleto:text:Responsable Tecnico',
            'precio',
            [
                'attribute'  => 'descuento',
                'format'=>'raw',
                'value'  => function ($model) {
                        return '( '.$model->descuento.' ) '.Html::button('Aplicar Descuento',['class'=>'btn btn-warning','onclick'=> 'aplicarDescuento('.json_encode($model->attributes).')']);
                                }
                
                ],
            'valor_total',
            //'abono',
            'pagado',
           // 'cerrada',
        ],
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
										                          ->where(['analisis.tipo_analisis_id'=>$categoria->id,'orden_id'=>$model->id])
										                           ->all(), 'id','id'), 
												    
												    ArrayHelper::map(
												        Examen::find()
												        ->joinWith(['analisis'])
												        ->where(['analisis.tipo_analisis_id'=>$categoria->id,'orden_id'=>$model->id])->all(),
												        'id', 'nombreCosto'),
												    ['separator'=>'<br/>', 'itemOptions'=>['class'=>'filled-in'],'labelOptions'=>['class'=>'container']]); 
												?>
										
									</div>
								</div>
								</div>
							
    
 <?php } ?>
</div>
 
    

</div>

