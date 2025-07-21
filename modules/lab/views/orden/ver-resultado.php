<?php
use app\assets\OrdenPageAsset;
use yii\bootstrap\Tabs;

OrdenPageAsset::register($this);

?>

<div class="orden-form">


 <?php if(isset($orden)){
  
     echo "<h4> $orden->detalle </h4>";
    
     $items=[];
    $examenes =$orden->examens;   
    foreach ( $examenes as $item) { 
         $items[]= [
             'label'=>$item->analisis->nombre,
             'encode'=>false,
             'content'=>$item->getResultado(),
         ];
     }
    
       
     echo Tabs::widget([
     'items'=>$items,     
     'encodeLabels'=>false
     ]);
     
     
 ?>
  <hr> 
  <div class="row text-center">
  <div class="col-md-12">
       <button class="btn btn-primary mt-2" onclick="imprimirOrden( <?=$orden->id ?>)"> <i class="mdi mdi-printer mr-2"></i>Imprimir Orden</button>
  </div>
  </div>
   


<?php } ?>

</div>

