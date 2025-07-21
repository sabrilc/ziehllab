<?php

use app\models\Sexo;
use app\models\User;
use yii\bootstrap\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Orden */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(['id'=>'fmAddCliente','action'=>Url::to(['/orden/add-cliente'])]); $cliente = new User(); ?>
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
    	   <div class="col-sm-12 col-md-3"> <?= $form->field($cliente, 'email')->textInput(['maxlength' => true]) ?> </div>
    	   <div class="col-sm-12 col-md-3"> <?= $form->field($cliente, 'email_notificacion')->textInput(['maxlength' => true]) ?> </div>       
    	  <div class="col-sm-12 col-md-3"> <?= $form->field($cliente, 'username')->textInput(['maxlength' => true]) ?> </div>
    	  <div class="col-sm-12 col-md-3"> <?= $form->field($cliente, 'password')->textInput(['maxlength' => true]) ?> </div>
    	 
    	  <div class="col-sm-12 col-md-3">  <?= $form->field($cliente, 'activo')->checkbox() ?></div>
    	  
    	   <div class="col-sm-12 col-md-12">
    	   		 <?= $form->field($cliente, 'direccion')->textarea(['rows' => 3,'class'=>'form-control']) ?>
    	   </div>
    	      
    	  <div class="col-sm-12 col-md-5 col-md-offset-5">    	 
    	    <?= Html::button('Guadar cliente', ['class' => 'btn btn-primary','style'=>'margin-top:7%;','onClick'=>'guardarCliente()']) ?>
    	  </div>
	
</div>
<?php ActiveForm::end(); ?>
