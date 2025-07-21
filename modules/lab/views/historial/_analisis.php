<?php


/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
use yii\helpers\Html;

?>

<div class="row">
   <div class="col-md-12"> 
 <?php foreach ( $analisis as $examen) { ?>
        <?= Html::a( $examen['nombre']." ( ". $examen['numero']." )" ,'javascript:verHistorial('. $examen['paciente_id'].','. $examen['id'].')',['class'=>'btn btn-primary',  'style'=>"margin: 5px;"] ) ?>

 <?php }?>
 
   </div>
</div>
