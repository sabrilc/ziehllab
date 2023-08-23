<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">    
        
    <div id="loginbox" class="mainbox col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3"> 
        
        <div class="row">                
            <div class="iconmelon">
              <img src="/imagen/login.jpg" width="70" height="50" alt="Inicio de sesión">
            </div>
        </div>
        
        <div class="panel panel-default" >
            <div class="panel-heading">
                <div class="panel-title text-center">Inicio de sesión</div>
            </div>
			<div class="panel-body">            
            <?php
                $form = ActiveForm::begin([
                                'id' => 'login-form',
                                'layout' => 'horizontal',
                                'fieldConfig' => [
                                    'template' => "{input}",
                                    'options' => [
                                        'tag' => 'span'
                                    ]
                                ],
                                
            
            ]); ?>                                   
               <?= $form->field($model, 'username',[
                   'template' => '<div class="input-group"> <span class="input-group-addon"> <i class="glyphicon glyphicon-user"> </i> </span>{input} {error}{hint} </div>'                   
                    ])->textInput([
                        'autofocus' => true,'class'=>'form-control',
                        'placeholder'=>'usuario'
                        
                    ])->label(false) ?>                                    
             
               
        
                <?= $form->field($model, 'password',[ 
                    'template' => '<div class="input-group"> <span class="input-group-addon"> <i class="glyphicon glyphicon-lock"> </i> </span>{input} {error}{hint} </div>'                    
                     ])->passwordInput([
                         'class'=>'form-control',
                         'placeholder'=>'password']) ?>                      
              <br>
				<div class="form-group">
					<!-- Button -->
					<div class="col-sm-12 controls">
                            <?= Html::submitButton('<i class="glyphicon glyphicon-log-in"></i> Entrar', ['class' => 'btn  btn-primary pull-right', 'name' => 'login-button']) ?>
                   			                 
                        </div>
				</div>

				<hr>
				<div class="form-group">
					<!-- Button -->
					<div class="col-sm-12 controls">
                           <?= Html::a('Recuperar acceso', ['site/request-password-reset'],['class'=>'btn btn-primary pull-left'] ) ?>                    
                        </div>
				</div>
                   
        
            <?php ActiveForm::end(); ?>
			</div>
		</div>  
    </div>
</div>


