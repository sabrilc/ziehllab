<?php

use app\models\Sexo;
use app\models\User;
use yii\bootstrap\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\View;
use app\assets\FontAwesomeIconsAsset;
use app\assets\HistorialPageAsset;
use app\assets\TypeaheadJSAsset;

/* @var $this yii\web\View */
/* @var $model app\models\Orden */
/* @var $form yii\widgets\ActiveForm */
FontAwesomeIconsAsset::register($this);
TypeaheadJSAsset::register($this);
HistorialPageAsset::register($this);

?>


<?php $form = ActiveForm::begin(['id'=>'fmCliente','action'=>Url::to(['/historial/analisis'])]); $cliente = new User(); ?>
<div class="row"> 

    	   <div class="col-sm-12 col-md-6  col-md-offset-3">
    	 <span class="fa fa-Grid form-control-feedback"></span>
    	    <?= Html::input('text','Grid','',[ 'class'=>"form-control typeahead Grid"]) ?> 
    	    <?= $form->field($cliente, 'id')->hiddenInput()->label(false) ?> 
    	   </div>
</div>
<div class="row" style="padding: 10px; background-color: floralwhite; margin: 20px;"> 
           <div class="col-sm-12 col-md-3">    	 
    	   		 <?= $form->field($cliente, 'identificacion')->textInput([ 'class'=>"form-control"]) ?> 
    	    </div>
    	    	    
    	     <div class="col-sm-12 col-md-9">    	 
    	    	<?= $form->field($cliente, 'nombres')->textInput([ 'class'=>"form-control"]) ?> 
    	    </div>    	  
    	  
    	  <div class="col-sm-12 col-md-4"> <?= $form->field($cliente, 'edad')->textInput(['maxlength' => true]) ?> </div>
    	  <div class="col-sm-12 col-md-4">  <?= $form->field($cliente, 'unidad_tiempo')->dropDownList(
                                                 ['RN'=>'RN','DIAS'=>'DIAS','MESES'=>'MESES',  'AÑOS' => 'AÑOS'],
                                                     ['prompt' => 'Seleccionar ...'     
                                                 ])?>
          </div>
          
          <div class="col-sm-12 col-md-4">
        	   <?= $form->field($cliente, 'sexo_id')->dropDownList(
        	       ArrayHelper::map( Sexo::find()->orderBy('id')->all(),'id','descripcion' ),
        	      ['prompt'=>'Seleccione..']) ?> 
    	  </div>
          
    	 
</div>
<?php ActiveForm::end(); ?>

 <div id="content"> </div>

