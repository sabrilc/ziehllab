<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


?>

<?=$this->render('_form_responsables',['model' =>$orden]) ?>

<div class='row mt-3 animated fadeIn'>
                  <div class='col-xs-6 col-sm-3 sidebar-offcanvas'>
                <div class='panel panel-primary'>
                    <div class='panel-heading'>Análisis</div>                 
                        <div class='list-group'>
                            <?php foreach ($orden->examens as $item) { ?>
                                <a  class='list-group-item'  onClick=cargarPlantillaExamen(<?=$item->id?>)> <?= $item->analisis->nombre?></a>
                            <?php } ?>
                        </div>
                        <div class='btn-group' role='group' aria-label='Opciones'>
                          <button type='button' class='btn btn-primary'  onclick='finalizarOrden(<?=$orden->id?>)'>Finalizar Orden </button>
                         <!--<button type='button' class='btn btn-default'  onclick='imprimirOrden(<?=$orden->id?>)'>Imprimir Orden</button>-->
						 <div id="orden-print-pdf" data-ordenid="<?=$orden->id?>"></div>
                        </div>                   
                     
                     </div> 
                    </div>  
              

                 <div class='col-xs-12 col-sm-9'>                
                    <div class='panel panel-default'>
                        <div class='panel-heading'>Plantilla</div>
                         <div class='panel-body'  id='area_de_trabajo'> </div>                 
                    
                    </div>
                 </div>
               </div>

<div class='row mt-3 animated fadeIn'>
    <div class='col-xs-12 col-sm-12'>
        <div class='panel panel-primary'>
            <div class='panel-heading'>Orden</div>
            <div class='panel-body'
            <div class='row'>
                <?php $form = ActiveForm::begin(['options' => [
                    'class' => 'animated fadeIn',
                    'id' => 'formularioOrden',
                    'onsubmit' => 'return guardarInfoOrden()',
                ]]) ?>


                <?= Html::activeHiddenInput($orden,'_id') ?>
                <div class="col-md-6"> <?= $form->field($orden,'paciente_info')->textarea(['rows' => '3', 'onBlur'=>'guardarInfoOrden()', 'maxlength' => true,'placeholder'=> 'Se muestra unicamente en el formato AccessLab'])?></div>
                <div class="col-md-6"> <?=$form->field($orden,'solicitante_info')->textarea(['rows' => '3', 'onBlur'=>'guardarInfoOrden()', 'maxlength' => true,'placeholder'=> 'Se muestra unicamente en el formato AccessLab'])?></div>
                <div class="col-md-12">
                    <?=$form->field($orden,'fecha_resultados')->textInput(['onChange'=>'guardarInfoOrden()'])?>
                    <?=$form->field($orden,'hora_resultados')->textInput()?>
                </div>
                <?php ActiveForm::end() ?>
            </div>

        </div>
    </div>
</div>

<script>
       $( function() {
        $( '#ordenbussines-fecha_resultados' ).datepicker($.extend({}, $.datepicker.regional['es'], { "dateFormat":"yy-mm-dd"}));
        $('#ordenbussines-hora_resultados').clockTimePicker( {
             onChange: function(newVal, oldVal) { guardarInfoOrden(); }, })
        $('#ordenbussines-hora_resultados').clockTimePicker('value', ' <?=date('H:i', strtotime( '2000-01-01 ' . $orden->hora_resultados)) ?>');


      } )
       inicializarSelect2()
    
		  const container = document.getElementById("orden-print-pdf");
		  const props = {
			ordenId: container.dataset.ordenid			
		  };
		  
		 
		  const root = ReactDOM.createRoot(container);
		  root.render(React.createElement(PdfModal, props));
 </script>