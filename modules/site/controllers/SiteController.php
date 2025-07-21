<?php

namespace app\modules\site\controllers;

use app\modules\lab\models\Orden;
use app\modules\site\forms\ContactForm;
use app\modules\site\forms\LoginForm;
use app\modules\site\forms\PasswordResetRequestForm;
use app\models\Registro;
use app\modules\site\forms\ResetPasswordForm;
use app\modules\site\models\Settings;
use app\modules\site\models\User;
use Yii;
use yii\base\InvalidArgumentException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

class SiteController extends Controller
{
   
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['login','requestPasswordReset','resetPassword'],
                        'allow' => true,
                        'roles' => ['?'], //visitantes
                    ],
                    [
                        'actions' => ['logout', 'acount'],
                        'allow' => true,
                        'roles' => ['@'], //logueado
                    ],
                    [
                        'actions' => ['index','descarga-resultados','analisis'],
                        'allow' => true,
                        'roles' => ['@','?'], //logueado
                    ],
                    
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                   // 'logout' => ['post'],
                ],
            ],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    
   /**
    * Funcion que realiza el inicio de session
    * @return \yii\web\Response|string
    */
    public function actionLogin()
    {

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }    

  /**
   * Funcion que realiza el cierre de session
   * @return \yii\web\Response
   */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    

    public function actionAnalisis()
    {
        return $this->render('analisis');
    }
    
    public function actionAcount()
    {
        $model = User::findOne(['id'=> Yii::$app->user->identity->id]);
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            unset($model->password);
            Registro::onUpdated($model);
        }
        
        $model->password='';
        return $this->render('acount', [
            'model' => $model,
        ]);
    }
    
    public function actionDescargarResultados()
    {
        
       
        $model= new Orden();
        
        if ( $model->load( Yii::$app->request->post() ) ) {
            
            $orden = Orden::findOne(['codigo'=> $model->codigo, 'codigo_secreto' => $model->codigo_secreto]);
            if(isset($orden)){
                \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
                echo header("Content-type: application/pdf");
                echo header('Content-Disposition: attachment; filename='.$orden->codigo.'.pdf');
                return $orden->pdfForDownload();
            }
            
            Yii::$app->session->setFlash('warning','Codigo de orden invalido o codigo secreto incorrecto!.. ');
        }
        
        return $this->render('descargar-resultados',['model'=>$model]);
    }
    
    /**
     *
     *
     * Muestra la pagina de inicio luego de iniciar session
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
    
    public function actionNosotros()
    {
        return $this->render('about');
    }
    
    public function actionContacto()
    {
        $model = new ContactForm();
        $setting = Settings::findOne(1);
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');
            
            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,'setting'=>$setting
        ]);
    }

   /**
    * Grafica el formulario de recobrar clave y ademas envia el mensaje al correo con el link 
    * para resetear la clave
    * @return \yii\web\Response|string
    */
    public function actionRequestPasswordReset()
    {
   
        $model = new PasswordResetRequestForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
           // return $model->sendEmail();
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Se ha envido las intrucciones para el cambio de clave al correo suministrado.');
                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Disculpa, No hemos podido resetear tu clave.');
            }
            
        }
        
        return $this->render('passwordResetRequestForm', [
            'model' => $model,
        ]);
    }
  /**
   * Verifica el token enviado, grafica el formulario con los campos para cambio de clave.
   * resive los datos del mismo formulario y actualiza la clave
   * @param string $token
   * @throws BadRequestHttpException
   * @return \yii\web\Response|string
   */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');
            return $this->goHome();
        }
      
        return $this->render('resetPasswordForm', [
            'model' => $model,
    ]);
    }
}
