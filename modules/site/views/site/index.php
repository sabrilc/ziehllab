<?php

/* @var $this yii\web\View */


use app\modules\lab\models\Orden;
use app\modules\site\models\User;
use yii\bootstrap\Html;
use yii\db\Query;

$this->title = Yii::$app->name;
?>
				
<?= Html::img('@web/imagen/background.png', ['alt'=>Yii::$app->name,'style'=>'', 'class'=>"img-responsive" ]) ?>			
					
<div class="row">
<div class="col-md-12 text-center">
<h3>Servicios en línea</h3>
</div>
</div>		

<div class="row" style="margin-bottom: 20px;">
<div class="col-sm-4 col-md-3 text-center">

<div class="card" style="width: 30rem;">
<?= Html::img('@web/imagen/lab-logo.png', [ 'height'=>'150',  'class'=>"bd-placeholder-img card-img-to" ]) ?>
  <div class="card-body">
    <h5 class="card-title">Análisis.</h5>
    <p class="card-text"> Realizamos todo tipo de analisis de laboratorio clínico</p>
    <?= Html::a('ANÁLISIS',['/site/analisis'],['class'=>'btn btn-services'])?>
  </div>
</div>

</div>

<div class="col-sm-4 col-md-3 text-center">

<div class="card" style="width: 30rem;">
<?= Html::img('@web/imagen/cotizacion.png', ['height'=>'150',  'class'=>"bd-placeholder-img card-img-to" ]) ?>
  <div class="card-body">
    <h5 class="card-title">Cotizaciones.</h5>
    <p class="card-text"> Realice su cotización en linea, ahorre tiempo y dinero.</p>
     <?= Html::a('COTIZAR',['/cotizacion/nueva'],['class'=>'btn btn-services'])?>
  </div>
</div>

</div>

<div class="col-sm-4 col-md-3 text-center">

<div class="card" style="width: 30rem;">
<?= Html::img('@web/imagen/clientes.png', ['height'=>'150',  'class'=>"bd-placeholder-img card-img-to" ]) ?>
  <div class="card-body">
    <h5 class="card-title">Acceso de Clientes.</h5>
    <p class="card-text">Acceso a nuestro sistema para consultar resultados</p>
     <?= Html::a('ACCEDER COMO CLIENTE',['/site/login'],['class'=>'btn btn-services'])?>
  </div>
</div>

</div>

<div class="col-sm-4 col-md-3 text-center">

<div class="card" style="width: 30rem;">
<?= Html::img('@web/imagen/doctor.png', ['height'=>'150',  'class'=>"bd-placeholder-img card-img-to" ]) ?>
  <div class="card-body">
    <h5 class="card-title">Acceso de Medicos.</h5>
    <p class="card-text">Acceso a nuestro sistema para consultar resultados de sus Pacientes</p>
      <?= Html::a('ACCEDER COMO MÉDICO',['/site/login'],['class'=>'btn btn-services'])?>
  </div>
</div>

</div>
</div>		


    
       <!-- ======= Counts Section ======= -->
    <section class="counts section-bg">
      <div class="container">

        <div class="row no-gutters">

          <div class="col-lg-3 col-md-6 d-md-flex align-items-md-stretch">
            <div class="count-box">
              <i class="icofont-simple-smile"></i>
              <span data-toggle="counter-up">
              <?=  User::find()
                ->alias('u')
                ->innerJoin('auth_assignment', 'u.id = auth_assignment.user_id')
                ->where(['item_name' => 'cliente'])
                ->count()
        ?>
              </span>
              <p><strong>Nuestros clientes</strong> <br>Somos la elección preferida de clientes a la hora de análisis</p>              
            </div>
          </div>

          <div class="col-lg-3 col-md-6 d-md-flex align-items-md-stretch">
            <div class="count-box">
              <i class="icofont-document-folder"></i>
              <span data-toggle="counter-up">
              <?=  User::find()
                          ->alias('u')
                          ->innerJoin('auth_assignment', 'u.id = auth_assignment.user_id')
                          ->where(['item_name' => 'medico'])
                          ->count()
                  ?>
              </span>
              <p><strong>Medicos de Babahoyo</strong> <br>Confian en la veracidad de nuestros análisis</p>
             
            </div>
          </div>

          <div class="col-lg-3 col-md-6 d-md-flex align-items-md-stretch">
            <div class="count-box">
              <i class="icofont-live-support"></i>
              <span data-toggle="counter-up">
              <?= (new Query())->from('orden')
                                         ->join('inner join','examen','orden_id =orden.id')
                                         ->join('inner join','analisis','analisis_id =analisis.id')                                     
                                         ->count('analisis.id');?>
              </span>
              <p><strong>Análisis Realizados</strong><br> Analisis procesados desde el año 2019</p>
              </div>
          </div>

          <div class="col-lg-3 col-md-6 d-md-flex align-items-md-stretch">
            <div class="count-box">
              <i class="icofont-users-alt-5"></i>
              <span data-toggle="counter-up">
              <?= Orden::find()->where([ 'cerrada' => true ])->count()?>
              </span>
              <p><strong>Ordenes Procesadas</strong> <br>Cantidad de ordenes de análisis que ya han sido finalizadas</p>
             
            </div>
          </div>

        </div>

      </div>
    </section><!-- End Counts Section -->

  <!-- ======= Info Box Section ======= -->
    <section class="info-box py-0">
      <div class="container-fluid">

        <div class="row">

          <div class="col-lg-7 d-flex flex-column justify-content-center align-items-stretch  order-2 order-lg-1">

            <div class="content">
              <h3>Nuestros Valores y Principios<strong> son nuestra identidad</strong></h3>
              <p>
               Creemos que cada una de nuestras acciones, individuales y en equipo, deben estar enfocadas en lograr el bienestar de nuestros usuarios, mediante un servicio de excelencia que nos diferencia de manera positiva.
              </p>
            </div>

            <div class="accordion-list">
              <ul>
                <li>
                  <span>01 Ética</span> 
                  <div id="accordion-list-1" class="collapse show in" data-parent=".accordion-list">
                    <p>
                      Practicamos una conducta transparente, honesta y confiable en todos nuestros actos. Observamos el cumplimiento permanente de la ley y normativa vigente, y de los lineamientos internos.
                    </p>
                  </div>
                </li>

                <li>
                  <span>02 Confianza</span>
                  <div id="accordion-list-2" class="collapse show in" data-parent=".accordion-list">
                    <p>
                     Construimos relaciones de confianza al satisfacer las necesidades y superar las expectativas de nuestros usuarios y siendo responsables con nuestra conducta en el desarrollo de las actividades.
                    </p>
                  </div>
                </li>

                <li>
                  <span>03 Respeto</span> 
                  <div id="accordion-list-3" class="collapse show in" data-parent=".accordion-list">
                    <p>
                      Reconocemos el derecho de los demás (usuarios, clientes, colaboradores, prestadores, proveedores) al cumplir con nuestros deberes y obligaciones en un ambiente de respeto, cordialidad y con alto sentido humano.
                    </p>
                  </div>
                </li>

              </ul>
            </div>

          </div>

          <div class="col-lg-5 align-items-stretch order-1 order-lg-2">
          <div> <?= Html::img('@web/imagen/valores.jpg',['class' => "img-responsive", 'style'=>"margin-top: 80px;"]) ?> </div>
         
          </div>
        </div>

      </div>
    </section><!-- End Info Box Section -->


<div class="row"></div>
<div class="container-fluid">
<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1993.9228426958116!2d-79.54012602804455!3d-1.8017823454220043!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x902d29f61fdd9701%3A0xb28561383ee8bac8!2sZiehl+laboratorio!5e0!3m2!1ses-419!2sec!4v1562765378109!5m2!1ses-419!2sec" width="100%" height="450" frameborder="0" style="border:0" allowfullscreen=""></iframe>
</div>	
			