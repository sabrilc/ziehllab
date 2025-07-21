<?php

/* @var $this \yii\web\View */
/* @var $content string */
/* @var $setting app\models\Settings */

use app\assets\PaperThemeAsset;
use app\modules\site\models\Settings;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;


PaperThemeAsset::register($this);

$setting = Settings::findOne(1);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" dir="ltr">
<head>
    <script src="https://cdn.jsdelivr.net/npm/pace-js@latest/pace.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pace-js@latest/pace-theme-default.min.css">
  <meta  charset="<?= Yii::$app->charset ?>" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="description" content="ZiEHL - Laboratorio Clinico.">
   <?= Html::csrfMetaTags() ?>

   <title><?= Html::encode($this->title) ?></title>
   <?php $this->head() ?>
    <style>
        .pace {
            -webkit-pointer-events: none;
            pointer-events: none;

            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        .pace-inactive {
            display: none;
        }

        .pace .pace-progress {
            background: #8d3319;
            position: fixed;
            z-index: 2000;
            top: 0;
            right: 100%;
            width: 100%;
            height: 2px;
        }

        .navbar-inverse {
            background-color: <?= $setting->primary_color ?>!important;
            color: #fff!important;
        }
        .navbar-inverse .navbar-nav > .active > a, .navbar-inverse .navbar-nav > .active > a:hover, .navbar-inverse .navbar-nav > .active > a:focus {
            color: #fff!important;
            font-size: 16px;
            background-color: <?= $setting->secondary_color ?>!important;
        }
        a {
            color: <?= $setting->primary_color ?>;
            text-decoration: none;
        }
        .btn-services {
            background-color: <?= $setting->primary_color ?>!important;
            border-radius: 50px !important;
            color: #fff;
        }
        #footer .footer-top .social-links a {
            background:  <?= $setting->primary_color ?>;
            color: #fff;
            text-decoration: none;
        }

        #footer .footer-top .social-links a:hover {
            background:  <?= $setting->secondary_color ?>;
            color: #ffc100;
            text-decoration: none;
        }

    </style>
</head>

 <?php $this->beginBody() ?>
<body class="wrapper">
<?php
NavBar::begin( [
              // 'brandLabel' => Html::img('@web/imagen/ziehl.png', ['alt'=>Yii::$app->name,'style'=>'max-height: 100%; height: 100%; -o-object-fit: contain; object-fit: contain;']),
             'brandLabel'=>'ZIEHL',
             'options'=>['class'=>'navbar navbar-inverse navbar-fixed-top'],
             'innerContainerOptions'=>[ 'class'=> 'container-fluid']
]);
echo Nav::widget([
    'items' => [
        ['label' => 'INICIO', 'url' => ['site/index'],   'linkOptions' => [], ],
        ['label' =>  '<span class="nav-text">NOSOTROS</span>','encode' => false, 'url' => ['site/nosotros'],'visible'=> Yii::$app->user->isGuest  ],
        ['label' =>  '<span class="nav-text">CONTACTO</span>','encode' => false, 'url' => ['/site/contacto'], 'visible'=> Yii::$app->user->isGuest ],



        ['label' => '<span class="nav-text">Ordenes</span>','encode' => false, 'url' => ['/lab/orden/index'],'visible'=> \Yii::$app->user->can('operador')],
        ['label' => '<span class="nav-text">Ingreso de Resultados</span>','encode' => false, 'url' => ['/lab/orden/ingreso-resultado'],'visible'=> \Yii::$app->user->can('operador')],
		['label' => '<span class="nav-text">Previsualización de Ordenes </span>','encode' => false, 'url' => ['/lab/orden/index-con-analisis'],'visible'=> \Yii::$app->user->can('operador')],

        ['label' =>  '<span class="nav-text">Historial</span>','encode' => false, 'url' => ['/historial'],'visible'=> \Yii::$app->user->can('operador') || \Yii::$app->user->can('administrador')?true:false ],
        ['label' =>  '<span class="nav-text">Cotizaciones</span>','encode' => false, 'url' => ['/cotizacion/index'],'visible'=> \Yii::$app->user->can('operador')],
        [
            'label' => 'Usuarios',
            'items' => [
                ['label' => '<i class="mdi mdi-account-supervisor-circle"></i><span class="nav-text">Cliente</span>','encode' => false, 'url' => ['/cliente/index'],'visible'=> \Yii::$app->user->can('operador') || \Yii::$app->user->can('administrador')?true:false ],
                ['label' => '<i class="mdi mdi-account-supervisor"></i><span class="nav-text">Medicos</span>','encode' => false, 'url' => ['/medico/index'],'visible'=> \Yii::$app->user->can('operador')],
                ['label' => '<i class="mdi mdi-account-tie"></i><span class="nav-text">Operadores</span>','encode' => false, 'url' => ['/operador/index'],'visible'=> \Yii::$app->user->can('administrador')],
                ['label' => '<i class="mdi mdi-shield-account"></i><span class="nav-text">Administradores</span>','encode' => false, 'url' => ['/admin/index'],'visible'=> \Yii::$app->user->can('administrador')],
               ],
            'visible'=> \Yii::$app->user->can('operador') || \Yii::$app->user->can('administrador')?true:false
        ],

        [
            'label' => 'Informes',
            'items' => [
                        ['label' => '<i class="mdi mdi-file-chart"></i><span class="nav-text">Informe</span>','encode' => false, 'url' => ['/lab/orden/informe'],'visible'=> \Yii::$app->user->can('administrador')],
                        ['label' => '<i class="mdi mdi-cloud-download"></i><span class="nav-text">Descarga Ordenes</span>','encode' => false, 'url' => ['/lab/orden/descarga'],'visible'=> \Yii::$app->user->can('administrador')],
                       ],
            'visible'=> \Yii::$app->user->can('administrador')
        ],


        ['label' =>  '<span class="nav-text">Mis Análisis</span>','encode' => false, 'url' => ['/cliente/resultados'],'visible'=> \Yii::$app->user->can('cliente')],
        ['label' =>  '<span class="nav-text">Resultados de pacientes </span>','encode' => false, 'url' => ['/medico/resultados'],'visible'=> \Yii::$app->user->can('medico')],


        [
            'label' => 'Configuraciones',
            'items' => [
                ['label' =>  'Análisis','encode' => false, 'url' => ['/analisis/index'],'visible'=> \Yii::$app->user->can('operador')],
                ['label' =>  'Medidas','encode' => false, 'url' => ['/medida/index'],'visible'=> \Yii::$app->user->can('operador')],
                ['label' =>  'Métodos','encode' => false, 'url' => ['/metodo/index'],'visible'=> \Yii::$app->user->can('operador')],
                ['label' =>  'Tipos de análisis','encode' => false, 'url' => ['/tipo-analisis/index'],'visible'=> \Yii::$app->user->can('operador')],
                ['label' =>  'Antibioticos','encode' => false, 'url' => ['/antibiotico/index'],'visible'=> \Yii::$app->user->can('operador')],
                ['label' =>  'Tipos de muestras','encode' => false, 'url' => ['/tipo-muestra/index'],'visible'=> \Yii::$app->user->can('operador')],
                ['label' =>  'Gérmenes','encode' => false, 'url' => ['/germen/index'],'visible'=> \Yii::$app->user->can('operador')],
                ['label' =>  'Laboratoristas','encode' => false, 'url' => ['/laboratorista/index'],'visible'=> \Yii::$app->user->can('operador')],




            ],
            'visible'=> \Yii::$app->user->can('operador')
        ],
    ],
    'options' => ['class' =>'navbar-nav'], // set this to nav-tab to get tab-styled navigation
]);

echo Nav::widget([
    'items' => [

        [
            'label' => 'Entrar',
            'url' => ['site/login'],
            'visible' => Yii::$app->user->isGuest,
            'linkOptions' =>["class" =>"btn btn-login" ],
        ],


        [
            'label' => !Yii::$app->user->isGuest?Yii::$app->user->identity->nombres:'',
            'visible' => !Yii::$app->user->isGuest,
            'items' => [

                ['label' => 'Cuenta', 'url' => ['site/acount'] ],


                '<li role="presentation" class="divider"></li>',
                ['label' =>'Salir',
                 'url'=>'#','options' =>['onclick'=>'$( "#fmLogout" ).submit();'],]
            ],
            'linkOptions' =>["class" =>"btn btn-login" ],
        ],
    ],
    'options' => ['class' =>'navbar-nav navbar-right'], // set this to nav-tab to get tab-styled navigation
]);



NavBar::end();
?>

<?=Html::beginForm(['/site/logout'], 'post', ['id' => 'fmLogout']) . Html::endForm() ?>

                 <div class="container-fluid" style="margin-top: 70px; min-height: 700px;">

                   <?= Breadcrumbs::widget([ 'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [], ]) ?>


               <?php
                        $flashMessages = Yii::$app->session->getAllFlashes();
                        if ($flashMessages) {

                            foreach($flashMessages as $key => $message) {
                                switch ($key) {
                                    case 'success': echo '<div class="alert alert-success" role="alert" id="alert-flash">';
                                                    break;
                                    case 'warning': echo ' <div class="alert alert-warning" role="alert" id="alert-flash">';
                                                    break;
                                    case 'info':    echo ' <div class="alert alert-info" role="alert" id="alert-flash">';
                                                    break;
                                    case 'danger':   echo ' <div class="alert alert-danger " role="alert" id="alert-flash">';                                                    break;

                                    default:  echo ' <div class="alert alert-dark" role="alert" id="alert-flash">';
                                                break;
                                }

                               echo '<i class="mdi mdi-alert mr-1"></i>';
                                echo $message;
                                echo '</div>';

                            }
                        }
                        ?>
                     <div class="panel">
                         <div class="panel-body" style="padding: 25px!important;">
    				         <?= $content ?>
                         </div>
                     </div>

                </div>



                <!-- ======= Footer ======= -->
  <footer id="footer" style="margin-top: 15px;">
    <div class="footer-top">
      <div class="container">
        <div class="row">

          <div class="col-lg-3 col-md-6 footer-info">
            <h3 style="color: white;">Laboratorio ZIEHL</h3>

            <p>
                <?=$setting->address ?>
              <strong>Teléfono:</strong> <?=$setting->telefono ?><br>
              <strong>Correo Electrónico:</strong> <?=$setting->email ?><br>
            </p>
            <div class="social-links mt-3">
              <a href="#" class="twitter"><i class="mdi mdi-twitter"></i></a>
              <a href="#" class="facebook"><i class="mdi mdi-facebook"></i></a>
              <a href="#" class="instagram"><i class="mdi mdi-instagram"></i></a>
              <a href="#" class="google-plus"><i class="mdi mdi-skype"></i></a>
              <a href="#" class="linkedin"><i class="mdi mdi-linkedin"></i></a>
            </div>
          </div>

          <div class="col-lg-2 col-md-6 footer-links">
            <h4>Enlaces de Interés</h4>
            <ul>
              <li><i class="bx bx-chevron-right"></i>  <?= Html::a('Inicio',['/site/index'])?></li>
              <li><i class="bx bx-chevron-right"></i> <?= Html::a('Nosotros',['/site/nosotros'])?></li>
            </ul>
          </div>

          <div class="col-lg-3 col-md-6 footer-links">
            <h4>Servicios OnLine</h4>
            <ul>
              <li><i class="bx bx-chevron-right"></i>  <?= Html::a('Cotizaciones',['/site/index'])?></li>
              <li><i class="bx bx-chevron-right"></i> <?= Html::a('Acceso de Clientes',['/site/nosotros'])?></li>
              <li><i class="bx bx-chevron-right"></i> <?= Html::a('Acceso de Médicos',['/site/nosotros'])?></li>
            </ul>
          </div>

          <div class="col-lg-4 col-md-6 footer-newsletter">
            <h4>Información de Contacto</h4>
            <p>Ver información de contacto</p>
            <a href='https://wa.me/593<?=str_replace(' ', '', $setting->telefono) ?>?text="Hola, deseo realizarme exámenes, en su laboratorio"'>
                <?= Html::img('@web/imagen/WhatsAppButtonWhiteMedium.svg') ?>
                </a>



          </div>

        </div>
      </div>
    </div>

    <div class="container">
      <div class="copyright">
        &copy; Copyright <strong><span>Laboratorio ZIEHL</span></strong>. Todos los derechos reservados
      </div>
      <div class="credits">
        Diseñado por <a href="https://facebook.com/sergio.josue.abril.campuzano">Ing. Sergio Abril C.</a>
      </div>
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>
<div id="overlay">
    <div class="loader"></div>
</div>
<?php $this->endBody() ?>
</body>
<?php $this->registerJs("
setTimeout(function(){  $('#alert-flash').remove(); }, 3000);
")?>

<?php $this->endPage() ?>
</html>

