<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

/* @var $setting app\models\Settings */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Contáctenos';
$this->params['breadcrumbs'][] = $this->title;
?>


    <!-- ======= Contact Us Section ======= -->
    <section id="contact" class="contact section-bg">

      <div class="container">
        <div class="section-title">
          <h2>Contáctenos</h2>
          <p>Solicite información en nuestra oficina o por los medios digitales y telefónico mostrados a continuación.</p>
        </div>
      </div>

      <div class="container-fluid">

        <div class="row">

          <div class="col-lg-6 d-flex align-items-stretch infos">

            <div class="row">

              <div class="col-lg-6 info d-flex flex-column align-items-stretch">
                <i class="bx bx-map"></i>
                <h4>Dirección</h4>
                <p><?=$setting->address ?></p>
              </div>
              <div class="col-lg-6 info info-bg d-flex flex-column align-items-stretch">
                <i class="bx bx-phone"></i>
                <h4>Télefono</h4>
                <p> <br><?=$setting->telefono ?></p>
              </div>
              <div class="col-lg-6 info info-bg d-flex flex-column align-items-stretch">
                <i class="bx bx-envelope"></i>
				<h4>Correo</h4>
                <p><?=$setting->email ?></p>
              </div>
              <div class="col-lg-6 info d-flex flex-column align-items-stretch">
                <i class="bx bx-time-five"></i>
                <h4>Horario de Atención</h4>
                <p><?=$setting->horario_atencion ?></p>
              </div>
            </div>
          </div>         
   
          <div class="col-lg-6 d-flex align-items-stretch contact-form-wrap">
          <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
              <div class="form-row">
                <div class="col-md-6">
                 <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>
                </div>
                <div class="col-md-6">
                   <?= $form->field($model, 'email') ?>
                </div>
              </div>             
                 <?= $form->field($model, 'subject') ?>
                 <?= $form->field($model, 'body')->textarea(['rows' => 3]) ?>
             
               <?= $form->field($model, 'verifyCode')->widget(Captcha::class, 
                   ['template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>', ]) ?>             
              
              <div class="mb-3">
                  <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')){ ?>
                    <div class="alert alert-success">
                        Thank you for contacting us. We will respond to you as soon as possible.
                    </div>
            
                    <p>
                        Note that if you turn on the Yii debugger, you should be able
                        to view the mail message on the mail panel of the debugger.
                        <?php if (Yii::$app->mailer->useFileTransport): ?>
                            Because the application is in development mode, the email is not sent but saved as
                            a file under <code><?= Yii::getAlias(Yii::$app->mailer->fileTransportPath) ?></code>.
                            Please configure the <code>useFileTransport</code> property of the <code>mail</code>
                            application component to be false to enable email sending.
                        <?php endif; ?>
                    </p>
            
                <?php } ?>              

              </div>
              <div class="text-center">
               <?= Html::submitButton('Enviar', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
              </div>
            <?php ActiveForm::end(); ?>
          </div>

        </div>

      </div>
    </section><!-- End Contact Us Section -->
    
    
