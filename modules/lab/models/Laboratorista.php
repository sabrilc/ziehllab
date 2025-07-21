<?php

namespace app\modules\lab\models;

use common\Tools;
use Yii;
use yii\helpers\Html;
use yii\web\UploadedFile;

/**
 * This is the model class for table "laboratorista".
 *
 * @property int $id
 * @property string $nombres
 * @property string $cargo
 * @property string $registro_msp
 * @property string $registro_senescyt
 * @property bool $responsable_tecnico
 * @property string $firma_digital_secret
 * @property string $firma_digital_fullname
 * @property string $dir_imagen_firma
 * @property string $dir_firma_digital
 * @property int $dbremove
 */


class Laboratorista extends \yii\db\ActiveRecord
{
    /**
     * @var UploadedFile
     */

    public $_p12_secret;
    public $imageFile;

    /**
     * @var UploadedFile
     */
    public $p12File;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laboratorista';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dbremove'], 'integer'],
            [['nombres', 'cargo', 'registro_msp', 'registro_senescyt','firma_digital_fullname'], 'string', 'max' => 200],
            [['dir_imagen_firma', 'dir_firma_digital'], 'string', 'max' => 400],
            [['responsable_tecnico'], 'integer'],
			[['identificacion'], 'string', 'max' => 10],
            [['firma_digital_secret'], 'string', 'max' => 100],
            [['imageFile'], 'file', 'skipOnEmpty' => True, 'extensions' => 'png, jpg'],
            [['p12File'], 'file', 'skipOnEmpty' => True, 'extensions' => 'p12'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
			'identificacion' => 'Identificación',
            'nombres' => 'Nombres',
            'cargo' => 'Cargo',
            'registro_msp' => 'Registro Msp',
            'registro_senescyt' => 'Registro Senescyt',
            'responsable_tecnico'=>'Es responsable_tecnico',
            'imageFile'=>'Imagen de firma manual',
            'p12File'=>'Archivo .p12 de firma digital',
            '_p12_secret'=>'Clave de seguridad de firma digital',
            'dbremove' => 'Dbremove',
        ];
    }

    public function upload()
    {
        if( !is_null( $this->imageFile)){
            $folder = __DIR__ . '/../media/imagen/firmas/' . $this->id . 'Laboratorista.php/';
            Tools::makeFolder($folder);
            $file = $folder. $this->imageFile->baseName . '.' . $this->imageFile->extension;
            Tools::removeFile(__DIR__ . '/ziehllab/' .$this->dir_imagen_firma);
            $this->imageFile->saveAs($file);
            $filedb='/media/imagen/firmas/'.$this->id.'/'. $this->imageFile->baseName . '.' . $this->imageFile->extension;
            $this->dir_imagen_firma = $filedb;
            $this->save(false);
        }
        if( !is_null( $this->p12File)){
            $folder = __DIR__ . '/../media/signatures/' . $this->id . 'Laboratorista.php/';
            Tools::makeFolder($folder);
            $file = $folder. $this->p12File->baseName . '.' . $this->p12File->extension;
            Tools::removeFile(__DIR__ . '/ziehllab/' .$this->dir_firma_digital);
            $this->firma_digital_fullname='';
            $this->p12File->saveAs($file);
            $filedb='/media/signatures/'.$this->id.'/'. $this->p12File->baseName . '.' . $this->p12File->extension;
            $this->dir_firma_digital = $filedb;
            $this->save(false);
            $this->getSignatureCN();


        }
    }

    public function getNombreCompleto()
    {
        return $this->nombres;
    }

    public function imageFirma()
    {
        return Html::tag("img",[],["src"=> Tools::imageToBase64(__DIR__ . '/ziehllab/' .$this->dir_imagen_firma),
            "width"=>"150px"
           ]);
    }

    public function imagenFirmaDigital()
    {
        if( strlen($this->firma_digital_fullname)>3){
        return Html::tag("div",
            Laboratorista . phpHtml::tag("div",
                Html::tag("img", [], ["src" => Tools::imageToBase64(__DIR__ . '/../media/imagen/defaults/Fingerprint.png'),
                    "width" => "150px"])
                , ["class" => "col col-md-2"]) . Html::tag("div",
                Html::tag("h4", $this->firma_digital_fullname, [])
                , ["class" => "col col-md-10"])
            ,["class"=>"row"]);
        }

        return '';



    }

    public function getSignatureCN()
    {
            $url =  \Yii::$app->params['signature_api'].'/api/sign/fullname';

            $files = [
                __DIR__ . '/ziehllab/' .$this->dir_firma_digital,
            ];
            $secrets=[Laboratorista::findOne(['id'=>$this->id])->firma_digital_secret];

            foreach ($files as $index => $file) {
                $postData['file_'.$index]= curl_file_create(realpath($file), mime_content_type($file),  basename($file));
            }

            foreach ($secrets as $index=> $secret){
                $postData['secret_'.$index] = $secret;
            }


            $request = curl_init($url);
            curl_setopt($request, CURLOPT_POST, true);
            curl_setopt($request, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($request, CURLOPT_VERBOSE, 0);
            $result = curl_exec($request);

            if ($result === false) {
                error_log(curl_error($request));
            }

            curl_close($request);

            $response= json_decode($result);

            if ($response->errors){
                $this->addError('p12File',$response->message );
            }else{
                $this->firma_digital_fullname= $response->fullname;
                $this->save(false);

            }






        }
}
