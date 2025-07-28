<?php


use yii\helpers\Html;
use app\assets\OrdenPageAsset;
use app\assets\ReactJsAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\lab\models\Orden */

OrdenPageAsset::register($this);
ReactJsAsset::register($this);

$this->title = $model->codigo;
$this->params['breadcrumbs'][] = ['label' => 'Inicio', 'url' => ['/']];
$this->params['breadcrumbs'][] = $this->title;

function badgeEstado($estado, $tipo = 'orden') {
    if ($tipo === 'orden') {
        return $estado === 'finalizada'
            ? '<span class="label label-success">Finalizada</span>'
            : '<span class="label label-warning">En Proceso</span>';
    } elseif ($tipo === 'pago') {
        return $estado
            ? '<span class="label label-primary">Pagada</span>'
            : '<span class="label label-danger">No Pagada</span>';
    }
}
?>


            <div class="panel-heading bg-primary">
                <h3 class="panel-title">
                    <i class="glyphicon glyphicon-list-alt"></i> Detalle de la Orden
                </h3>
            </div>

            <div class="panel-body">

                <div class="media">
                    <div class="media-left hidden-xs">
                        <i class="glyphicon glyphicon-user" style="font-size: 48px; color: #337ab7;"></i>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading"><?= Html::encode($model->paciente->nombreCompleto) ?></h4>
                        <p>
                            <strong>Código:</strong> <?= Html::encode($model->codigo) ?><br>
                            <strong>Edad:</strong> <?= Html::encode($model->paciente->getEdad()) ?> <br>
                            <strong>Fecha de nacimiento:</strong> <?= Yii::$app->formatter->asDate($model->paciente->fecha_nacimiento) ?><br>
                            <strong>Sexo:</strong> <?= ucfirst($model->paciente->sexo->descripcion) ?>
                        </p>
                    </div>
                </div>

                <hr>

                <div class="row text-center">
                    <div class="col-sm-6">
                        <h4><i class="glyphicon glyphicon-ok-circle text-success"></i> Estado de la Orden</h4>
                        <?= badgeEstado($model->cerrada?'finalizada':'', 'orden') ?>
                    </div>
                    <div class="col-sm-6">
                        <h4><i class="glyphicon glyphicon-credit-card text-primary"></i> Pago</h4>
                        <?= badgeEstado($model->pagado?'pagado':'', 'pago') ?>
                    </div>
                </div>

                <hr>

                <div class="text-center">
                        <div id="orden-print-pdf" data-ordenid="<?=$model->token?>"></div>

                </div>

            </div>

<?php $this->registerJsFile( '@web/static/js/resultadosPrintPdf.js', [ 'position'=> \yii\web\View::POS_END]) ?>

<?php $this->registerJs( <<<JS
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById("orden-print-pdf");
    const props = {
        ordenId: container.dataset.ordenid
    };


    const root = ReactDOM.createRoot(container);
    root.render(React.createElement(PdfModal, props));
   
})

   
JS, \yii\web\View::POS_END); ?>


