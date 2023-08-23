<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Nosostros';
$this->params['breadcrumbs'][] = $this->title;
?>
   <!-- ======= About Us Section ======= -->
    <section id="about" class="about">
      <div class="container">

        <div class="section-title">
          <h2><?= Html::encode($this->title) ?></h2>
          <p>Ademas de nuestros principios y valores tenemos bien definido lo que somos y lo que queremos ser.</p>
        </div>
        <div class="row">
          <div class="col-lg-6 pt-4 pt-lg-0 content">
            <h3>Nuestra <strong>visión.</strong></h3>
            <p class="font-italic">
             Ser líderes en proporcionar un servicio de alta calidad y calidez, a médicos y pacientes; manteniendo los más altos estándares tecnológicos, que permita brindar un servicio ágil y garantizado.
            </p>
               <h3>Nuestra <strong>misión.</strong></h3>
            <p>
            Ziehl Laboratorio tiene como misión ser parte integral como herramienta en el diagnóstico médico, y ofrecer la más alta calidad, fiabilidad y confianza a nuestros clientes, con resultados que están a la vanguardia de la excelencia tecnológica, profesional y humana.</p>
            
          </div>
        </div>

      </div>
    </section><!-- End About Us Section -->
    


   <!-- ======= Our Team Section ======= -->
    <section id="team" class="team">
      <div class="container">

        <div class="section-title">
          <h2>Nuestro Equipo</h2>
          <p>Somos un grupo de profesionales altamente calificados.</p>
        </div>

        <div class="row">

          <div class="col-md-12">
              <?= Html::img('@web/imagen/staff/workgroup.png', [  'class'=>"img-responsive"]) ?>
          </div>
        </div>

      </div>
    </section><!-- End Our Team Section -->

