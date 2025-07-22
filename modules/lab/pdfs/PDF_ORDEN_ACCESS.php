<?php
namespace app\modules\lab\pdfs;

use app\modules\site\models\Empresa;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Logo\Logo;
use utils\Endroid\QrCode;
use inquid\pdf\FPDF;
use utils\Texto;
use yii\db\Expression;
use utils\Endroid\QrCode\Writer\PngWriter;

class PDF_ORDEN_ACCESS extends FPDF
{

    public $examen=['analisis_id'=>null];
    private $empresa;
    public $orden;
    public $paciente;
    public $doctor;
    public $laboratorista;
    public $responsableTecnico;
    public $analisis;
    public $secciones;
    public $font;
    public $font_title_size;
    public $font_body_size;
    public $font_body_table_title;    
    public $line_begin;    
    private $debug=false;
    private $digital_sign= false;
	private $footer;

    private $font_sign;
    
    private $rapidTestIgm;
	 private $rapidTestIgg;
    private $hisopadoAntigeno;
	private $nCovIggClia;
	private $nCovIgmClia;
	private $sarsCov2SRbdIgg;
	private $antiSarsCov2;
	private $rTPCRtiempoReal;
    
    public function __construct($orden,$for_sign=false){
        $this->digital_sign = $for_sign;
        $this->rapidTestIgm = false;
		$this->rapidTestIgg = false;
        $this->hisopadoAntigeno = true;
		$this->nCovIggClia = false;
		$this->nCovIgmClia = false;
        $this->sarsCov2SRbdIgg = false;
		$this->antiSarsCov2 = false;
		$this->rTPCRtiempoReal = true;
		
        $this->empresa = Empresa::findOne(1);
        $this->orden=$orden;
        $this->paciente=$this->orden->paciente;
        $this->doctor=$this->orden->doctor;
        $this->laboratorista = $this->orden->laboratorista;
        $this->responsableTecnico = $this->orden->responsableTecnico;
        if( $this->digital_sign){
            if( $this->orden->fecha_resultados ==''){ $this->orden->fecha_resultados = new Expression(" current_date");$this->orden->save(); }
            if( $this->orden->hora_resultados ==''){ $this>$this->orden->hora_resultados = new Expression(" current_time");$this->orden->save(); }
        }
       
        $this->font='Arial';
        $this->font_sign='Courier';
        $this->font_title_size=12;        
        $this->font_body_size=8;
        $this->footer = true;
        $this->line_begin=15;
        parent::__construct();
        $this->AddPage('P','A4');            
 
        $this->generar();
        $this->SetAutoPageBreak(true,50);   
       
    }
    
    public function generar(){
        $numero_hoja_men=0;
        foreach ($this->orden->examenesParaImprimir as $examen) {
            $this->examen = $examen;
            $this->analisis = $this->examen->analisis;
            $numero_hoja=$this->analisis->hoja_impresion;
            if($numero_hoja!= $numero_hoja_men && 0 !=  $numero_hoja_men){  $this->AddPage('P','A4'); }

            if( $this->impresionAccessLab() ){
                $this->builtAccessLab();
				$this->footer = false;
            } else if( $this->impresionPanel()){
                $this->builtPanel();
                $this->footer = false;
            }

            else{
                $this->builtNormal();
				$this->footer = true;
            }
             
             $numero_hoja_men=$this->analisis->hoja_impresion;
        }
    }
    
    private function builtNormal(){
        $this->analisis();
        $this->germenes();        
        $this->textoNota( $this->examen->nota );
		    
    }

    private function builtPanel(){
        $this->encabezadoForPanel();
        $this->analisis();
        $this->germenes();
        $this->textoNota( $this->examen->nota );
        $this->firmaPanel();




    }

    private function firmaPanel(){
        $this->SetX($this->line_begin+10);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(70,5, Texto::encodeLatin1( 'Fecha y hora de emisión del informe de resultados:'),$this->debug, 0,'L',0);
        $date = new \DateTime($this->orden->fecha_resultados );
        $time = new \DateTime('2021-01-01 '.$this->orden->hora_resultados );
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(95,5, Texto::encodeLatin1(  'Babahoyo, '. $this->generateEnLetras( date_format($date, 'Y/m/d') ).' '.  date_format($time, 'H\Hi')   ),$this->debug, 0,'L',0);
        $this->ln(30);

        $y= $this->GetY();
        $this->SetFont($this->font,'',$this->font_body_size);
        if( !empty($this->orden->laboratorista_id ) && $this->orden->laboratorista_id <> 2 ){
            $laboratorista = $this->laboratorista;
            $this->Cell(90,3,Texto::encodeLatin1( $laboratorista->nombres), $this->debug ,0,'C');
            $this->Ln();
            $this->SetX($this->line_begin);
            $this->Cell(90,3,Texto::encodeLatin1('Rg. ACESS. '. $laboratorista->identificacion), $this->debug,0,'C');
            $this->Ln();
            $this->SetFont($this->font,'B',$this->font_body_size);
            $this->SetX(30);
            $this->MultiCell(50,3,Texto::encodeLatin1('Responsable de la emisión de los resultados de la prueba'), $this->debug,'C');
        }
            if( $this->digital_sign){
                if( strlen($laboratorista->firma_digital_fullname) > 0) {
                    $url = Texto::encodeLatin1("FIRMADO POR: " . $laboratorista->firma_digital_fullname .
                        chr(10) . "FECHA: " . $this->orden->fecha_firmado_digital);
                    $qrCode = QrCode::create($url);
                    $qrCode->setSize(250)->setMargin(5)->setForegroundColor(new Color(55, 55, 55));

                    $writer = new PngWriter();
                    $result = $writer->write($qrCode);
                    $dataUri = $result->getDataUri();
                    $this->Image($dataUri, $this->line_begin, $y - 20, 20, 20, 'png');
                    $this->SetXY($this->line_begin + 20, $y - 16);
                    $this->SetFont($this->font_sign, '', $this->font_body_size);
                    $this->Cell(90, 1, Texto::encodeLatin1("Firmado electrónicamente por:"), $this->debug, 0, 'J');
                    $this->ln(1);
                    $this->SetFont($this->font_sign, 'B', $this->font_title_size);
                    $this->SetXY($this->line_begin + 20, $y - 14);
                    $this->MultiCell(105, 4, Texto::encodeLatin1($this->subjectSignature($laboratorista->firma_digital_fullname)), $this->debug, 'J', 0);
                }
            }else{
            $urlImage = __DIR__ . '/ziehllab/' .$laboratorista->dir_imagen_firma;
            if (@getimagesize($urlImage)) {
                $this->Image( $urlImage, $this->GetX()+17, $this->GetY() - 28,60,  30,  pathinfo( $urlImage, PATHINFO_EXTENSION ) );
            }}


        $this->SetY($y);
        $this->SetFont($this->font,'',$this->font_body_size);
        $responsableTecnico = $this->responsableTecnico;
        if( !is_null($responsableTecnico)){
            $this->SetX(100);
            $this->Cell(90,3,Texto::encodeLatin1( $responsableTecnico->nombres), $this->debug ,0,'C');
            $this->Ln();
            $this->SetX(100);
            $this->Cell(90,3,Texto::encodeLatin1('Rg. ACESS. '.$responsableTecnico->identificacion), $this->debug,0,'C');
            $this->Ln();
            $this->SetX(120);
            $this->SetFont($this->font,'B',$this->font_body_size);
            $this->MultiCell(50,3,Texto::encodeLatin1('Responsable Técnico'), $this->debug,'C');

            if( $this->digital_sign){
                if( strlen($responsableTecnico->firma_digital_fullname) > 0) {
                    $url = Texto::encodeLatin1("FIRMADO POR: " . $responsableTecnico->firma_digital_fullname .
                        chr(10) . "FECHA: " . $this->orden->fecha_firmado_digital);
                    $qrCode = QrCode::create($url);
                    $qrCode->setSize(250)->setMargin(5)->setForegroundColor(new Color(55, 55, 55));

                    $writer = new PngWriter();
                    $result = $writer->write($qrCode);
                    $dataUri = $result->getDataUri();
                    $this->Image($dataUri, 120, $y - 20, 20, 20, 'png');
                    $this->SetXY(140, $y - 16);
                    $this->SetFont($this->font_sign, '', $this->font_body_size);
                    $this->Cell(90, 1, Texto::encodeLatin1("Firmado electrónicamente por:"), $this->debug, 0, 'J');
                    $this->ln(1);
                    $this->SetFont($this->font_sign, 'B', $this->font_title_size);
                    $this->SetXY(140, $y - 14);
                    $this->MultiCell(105, 4, Texto::encodeLatin1($this->subjectSignature($responsableTecnico->firma_digital_fullname)), $this->debug, 'J', 0);
                }
            }
            else {

                $this->SetX(100);
                $urlImage = __DIR__ . '/ziehllab/' .$responsableTecnico->dir_imagen_firma;
                if (@getimagesize($urlImage)) {
                    //$this->Image( $urlImage, $this->GetX()+25, $this->GetY() - 25,40,  20,  pathinfo( $urlImage, PATHINFO_EXTENSION ) );
                    $this->Image($urlImage, $this->GetX() + 17, $this->GetY() - 28, 60, 30, pathinfo($urlImage, PATHINFO_EXTENSION));
                }
            }

             }
        $this->ln(7);

        $y = $this->GetY();
        if( strlen($this->orden->token)>50 ){
            $url = 'https://'.$_SERVER['HTTP_HOST'].'/documentos/analisis?token='.$this->orden->token;
            $qrCode = (new QrCode($url));
        } else{
            $url=$this->orden->codigo;
        }

        $qrCode = QrCode::create($url);
        $qrCode->setSize(250)->setMargin(5)->setForegroundColor(new Color(55, 55, 55));

        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $dataUri = $result->getDataUri();
        $this->Image($dataUri,$this->line_begin,$y+20,20,20,'png');
        $y = $y+40;
        $this->SetXY($this->line_begin + 20,$y-16);
        $this->Cell(90,1,Texto::encodeLatin1("ziehllab.com"), $this->debug ,0,'J');
        $this->ln(1);
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->SetXY($this->line_begin + 20,$y-14);
        $this->MultiCell(105,4,Texto::encodeLatin1('Escanea este codigo QR'. chr(10).'para visualizar el documento  digital'),$this->debug ,'J',0);

        $this->ln(100);
    }
    
    private function builtAccessLab(){
      $this->encabezadoAccessLab();
      $this->analisisAccessLab();
    }
	
	
    
    private function encabezadoAccessLab(){
	   $this->checkY();
	   $this->Image(__DIR__ . '/../../../media/imagen/app/AccessLab_a4.png',0,0,210,297,'png');
       $this->Image(__DIR__ . '/../../../media/imagen/app/ziehllab_logo.png',$this->line_begin+5, 9, 70, 28, 'png');
        $this->SetXY( $this->line_begin + 75, 10);
      //  $this->SetFont($this->font,'B',$this->font_body_size);
      //  $this->Cell(90,5,Texto::encodeLatin1($this->empresa->razon_social),$this->debug,0,'L');
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->MultiCell(105,5,Texto::encodeLatin1('Dirección: '. chr(10).$this->empresa->direccion.  chr(10).'Télefono: '.$this->empresa->telefono),$this->debug ,'J',0);       
        $this->ln();
        
        $this->SetX($this->line_begin);
        $this->SetFont($this->font,'B',$this->font_body_size);
		$y = $this->GetY();
		$x = $this->GetX();
		$this->SetFillColor( 253, 210, 195 );
		$this->SetDrawColor( 254, 254, 254 );
		$this->Cell(185,25,Texto::encodeLatin1(''),$this->debug,0,'L',true);
        $this->Line( $this->line_begin, $y+20, 200, $y+20);//horizontal
		$this->Line( $this->line_begin +90, $y, $this->line_begin +90, $y+25);//vertical 1
		$this->Line( $this->line_begin +135, $y, $this->line_begin +135, $y+20);//vertical 2
        $this->SetXY($x,$y);
        $this->Cell(90,5,Texto::encodeLatin1('DATOS DEL PACIENTE'),$this->debug,0,'L');		
		$x = $this->GetX();
		$this->MultiCell(45,5,Texto::encodeLatin1('INFORMACIÓN DEL PACIENTE'),$this->debug,'L');
		$this->SetX($x);
		$this->SetFont($this->font,'',$this->font_body_size);
		$this->MultiCell(45,3,Texto::encodeLatin1( $this->orden->paciente_info),$this->debug,'J');
		$this->SetXY(150,$y);
		$this->SetFont($this->font,'B',$this->font_body_size);
		$this->MultiCell(50,4,Texto::encodeLatin1('INFORMACIÓN DEL'. chr(10).'SOLICITANTE DE LA PRUEBA'),$this->debug,'L');
		$this->SetX(150);
		$this->SetFont($this->font,'',$this->font_body_size);
		
		if( $this->orden->doctor_id > 0 ){
			$this->MultiCell(50,3,Texto::encodeLatin1( $this->orden->doctor->nombres),$this->debug,'J');
		}else{
		$this->MultiCell(50,3,Texto::encodeLatin1( $this->orden->solicitante_info),$this->debug,'J');
		}
		$this->SetY($y);
        $this->ln(5);
        $this->SetX($this->line_begin);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(20,5,Texto::encodeLatin1('Nombres:'),$this->debug,0,'L');
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(70,5,Texto::encodeLatin1($this->paciente->nombres),$this->debug,0,'L');
		
        $this->ln();
        $this->SetX($this->line_begin);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(25,5,Texto::encodeLatin1('Identificación:'),$this->debug,0,'L');
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(65,5,Texto::encodeLatin1( strlen( $this->paciente->identificacion ) > 2 ? $this->paciente->identificacion:'' ),$this->debug,0,'L');
        $this->ln();
        
        $this->SetX($this->line_begin);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(12,5,Texto::encodeLatin1('Edad:'),$this->debug,0,'L');
        $this->SetFont($this->font,'',$this->font_body_size);
        if($this->paciente->unidad_tiempo=='RN'){           
            $this->Cell(78,5,Texto::encodeLatin1('RN'),$this->debug,0,'L');           
        }else{
            $this->Cell(10,5,Texto::encodeLatin1($this->paciente->edad),$this->debug,0,'L');
            $this->Cell(68,5,Texto::encodeLatin1($this->paciente->unidad_tiempo),$this->debug,0,'L');           
        }
        $this->ln();
        $this->SetX($this->line_begin);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(42,5,Texto::encodeLatin1('Fecha de toma de la muestra:'),$this->debug,0,'L');
        $this->SetFont($this->font,'',$this->font_body_size);
        $date = new \DateTime($this->orden->fecha);
        $this->Cell(48,5,Texto::encodeLatin1( $this->generateEnLetras( date_format($date, 'Y/m/d') ) ),$this->debug,0,'L');  
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(25,5,Texto::encodeLatin1('Número de orden: '),$this->debug,0,'L');
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(70,5,Texto::encodeLatin1($this->orden->codigo),$this->debug,0,'L');
        $this->ln(7);
    }


    private function encabezadoForPanel(){
        $this->checkY();
        $this->Image(__DIR__ . '/../../../media/imagen/app/AccessLab_a4.png',0,0,210,297,'png');
        $this->Image(__DIR__ . '/../../../media/imagen/app/ziehllab_logo.png',$this->line_begin+5, 9, 70, 28, 'png');
        $this->SetXY( $this->line_begin + 75, 10);
        $this->SetFont($this->font,'',$this->font_body_size);
        $y = $this->GetY();
        $this->MultiCell(105,5,Texto::encodeLatin1('Dirección: '. chr(10).$this->empresa->direccion.  chr(10).'Télefono: '.$this->empresa->telefono),$this->debug ,'J',0);
        $this->SetXY( $this->line_begin +161 ,$y+24);
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->MultiCell(30,5,Texto::encodeLatin1('Unicodigo: '.$this->empresa->unicodigo),$this->debug ,'J',0);

        $this->ln();
        $url = $this->empresa->access_url;
        $qrCode = QrCode::create($url);
        $qrCode->setSize(250)->setMargin(5)->setForegroundColor(new Color(55, 55, 55));

        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $dataUri = $result->getDataUri();
        $this->Image($dataUri,$this->line_begin+160,$y,25,25,'png');

        $this->SetXY($this->line_begin,40);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $y = $this->GetY();
        $x = $this->GetX();
        $this->SetFillColor( 253, 210, 195 );
        $this->SetDrawColor( 254, 254, 254 );
        $this->Cell(185,30,Texto::encodeLatin1(''),$this->debug,0,'L',true);
        $this->Line( $this->line_begin, $y+25, 200, $y+25);//horizontal
        $this->Line( $this->line_begin +90, $y, $this->line_begin +90, $y+30);//vertical 1
        $this->Line( $this->line_begin +135, $y, $this->line_begin +135, $y+25);//vertical 2
        $this->SetXY($x,$y);
        $this->Cell(90,5,Texto::encodeLatin1('DATOS DEL PACIENTE'),$this->debug,0,'L');
        $x = $this->GetX();
        $this->MultiCell(45,5,Texto::encodeLatin1('INFORMACIÓN DEL PACIENTE'),$this->debug,'L');
        $this->SetX($x);
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->MultiCell(45,3,Texto::encodeLatin1( $this->orden->paciente_info),$this->debug,'J');
        $this->SetXY(150,$y);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->MultiCell(50,4,Texto::encodeLatin1('INFORMACIÓN DEL'. chr(10).'SOLICITANTE DE LA PRUEBA'),$this->debug,'L');
        $this->SetX(150);
        $this->SetFont($this->font,'',$this->font_body_size);

        if( $this->orden->doctor_id > 0 ){
            $this->MultiCell(50,3,Texto::encodeLatin1( $this->orden->doctor->nombres),$this->debug,'J');
        }else{
            $this->MultiCell(50,3,Texto::encodeLatin1( $this->orden->solicitante_info),$this->debug,'J');
        }
        $this->SetY($y);
        $this->ln(5);
        $this->SetX($this->line_begin);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(20,5,Texto::encodeLatin1('Nombres:'),$this->debug,0,'L');
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(70,5,Texto::encodeLatin1($this->paciente->nombres),$this->debug,0,'L');

        $this->ln();
        $this->SetX($this->line_begin);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(25,5,Texto::encodeLatin1('Identificación:'),$this->debug,0,'L');
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(65,5,Texto::encodeLatin1( strlen( $this->paciente->identificacion ) > 2 ? $this->paciente->identificacion:'' ),$this->debug,0,'L');
        $this->ln();

        $this->SetX($this->line_begin);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(25,5,Texto::encodeLatin1('Sexo:'),$this->debug,0,'L');
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(65,5,Texto::encodeLatin1( strlen( $this->paciente->sexo_id ) > 0 ? $this->paciente->sexo->descripcion:'' ),$this->debug,0,'L');
        $this->ln();

        $this->SetX($this->line_begin);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(12,5,Texto::encodeLatin1('Edad:'),$this->debug,0,'L');
        $this->SetFont($this->font,'',$this->font_body_size);
        if($this->paciente->unidad_tiempo=='RN'){
            $this->Cell(78,5,Texto::encodeLatin1('RN'),$this->debug,0,'L');
        }else{
            $this->Cell(10,5,Texto::encodeLatin1($this->paciente->edad),$this->debug,0,'L');
            $this->Cell(68,5,Texto::encodeLatin1($this->paciente->unidad_tiempo),$this->debug,0,'L');
        }
        $this->ln();
        $this->SetX($this->line_begin);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(42,5,Texto::encodeLatin1('Fecha de toma de la muestra:'),$this->debug,0,'L');
        $this->SetFont($this->font,'',$this->font_body_size);
        $date = new \DateTime($this->orden->fecha);
        $this->Cell(48,5,Texto::encodeLatin1( $this->generateEnLetras( date_format($date, 'Y/m/d') ) ),$this->debug,0,'L');
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(25,5,Texto::encodeLatin1('Número de orden: '),$this->debug,0,'L');
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(70,5,Texto::encodeLatin1($this->orden->codigo),$this->debug,0,'L');
        $this->ln(7);
    }
    
    private function analisisAccessLab(){
           $this->SetX($this->line_begin+5);
		     $this->SetFont($this->font,'B',$this->font_body_size);
           $this->Cell(85,5, Texto::encodeLatin1('MÉTODO:'),$this->debug, 0,'L',0);
           $this->ln();
           $this->SetX($this->line_begin+10);
		     $this->SetFont($this->font,'',$this->font_body_size);
           $this->Cell(165,5, Texto::encodeLatin1($this->examen->analisis->nombre ),$this->debug, 0,'L',0);
           $this->Ln();
            $parametros=$this->examen->analisis->parametrosParaIngresoResultado;
            foreach ($parametros as $parametro) {             
                $this->SetX($this->line_begin+10);
                $this->SetFont($this->font,'',$this->font_body_size);
                $parametroExamen=$parametro->getExamenParametros()->where([ 'examen_id' => $this->examen->id, 'parametro_id' => $parametro->id ])->One();
                
                if(isset($parametroExamen)){                    
                    if( !empty( $parametroExamen->valor ) or Texto::trim($parametroExamen->valor) != '' ){
                        $this->checkY();
                        if(isset($parametro->metodo_id)){
                            $this->SetX($this->line_begin+15);
                            $this->SetFont($this->font,'',$this->font_body_size);
                            $this->Cell(165,5, Texto::encodeLatin1($parametro->metodo->descripcion),$this->debug,0,'L',0);                           
                        }
                        $this->ln(7);                        
                        $this->SetX($this->line_begin+5);
						$this->SetFont($this->font,'B',$this->font_body_size);
                        $this->Cell(85,5, Texto::encodeLatin1('ENSAYO:'),$this->debug, 0,'L',0);
                        $this->ln();
                        $this->SetX($this->line_begin+10);
						$this->SetFont($this->font,'',$this->font_body_size);
                        $this->MultiCell(165,5, Texto::encodeLatin1( $parametro->ensayo ),$this->debug ,'J', 0 );
                        $this->ln(7);
                        
                        $this->SetX($this->line_begin+10);
						$this->SetFont($this->font,'B',$this->font_body_size);
                        $this->Cell(85,5, Texto::encodeLatin1('Amplificación y detección:'),$this->debug, 0,'L',0);
                        $this->ln();
                        $this->SetX($this->line_begin+10);
						$this->SetFont($this->font,'',$this->font_body_size);
                        $this->MultiCell(165,5, Texto::encodeLatin1(  $parametro->amplificacion_deteccion ),$this->debug ,'J', 0 );
                        $this->ln(7);
                        
                        $this->SetX($this->line_begin+10);
						$y = $this->GetY();
						$x = $this->GetX();
						$this->SetFillColor( 253, 210, 195 );
						$this->Cell(165,15,Texto::encodeLatin1(''),$this->debug,0,'L',true);
		                $this->Line( $this->line_begin + 10, $y + 5, $this->line_begin +175, $y + 5);//horizontal 1	
						$this->Line( $this->line_begin + 10, $y + 10, $this->line_begin +175, $y + 10 );//horizontal 2	
						$this->Line( $this->line_begin + 90, $y + 10, $this->line_begin + 90, $y + 15 );//vertical 1	
					   
						$this->SetXY($x,$y);
                        $this->SetFont($this->font,'B',$this->font_body_size);						
                        $this->Cell(165,5, Texto::encodeLatin1('Resultados del Análisis'),$this->debug,0,'C',0); 
                        $this->ln();
                        
                        $this->SetX($this->line_begin+10);
                        $this->Cell(165,5, Texto::encodeLatin1($this->examen->analisis->nombre ),$this->debug, 0,'C',0);
                        $this->ln();
                        
                        $this->SetX($this->line_begin+10);
						$this->SetFont($this->font,'',$this->font_body_size);
                        $this->Cell(75,5, Texto::encodeLatin1($parametro->descripcion ),$this->debug,0,'R',0); 
                        $this->SetX( $this->GetX() + 10 );
						$this->SetFont($this->font,'B',$this->font_body_size);
                        if( strlen( Texto::trim( $parametroExamen->medida ) ) == 0 && strlen( Texto::trim( $parametroExamen->valor ) ) > 10){                          
                            $this->Cell(80,5, Texto::encodeLatin1( Texto::trim($parametroExamen->valor )),$this->debug ,0,'L', 0 );
                        }else{                          
                            $this->Cell(80,5, Texto::encodeLatin1( Texto::trim($parametroExamen->valor.' '.$parametroExamen->medida )),$this->debug ,0,'L', 0 );                           
                        }                        
                        $this->ln(10);
                        
                        $this->SetX($this->line_begin+10);
						$this->SetFont($this->font,'B',$this->font_body_size);
                        $this->Cell(165,5, Texto::encodeLatin1( 'Intervalos de referencias:'),$this->debug, 0,'L',0);
                        $this->ln();
                        
                        $this->SetX($this->line_begin+10);
						$this->SetFont($this->font,'',$this->font_body_size);
                        $this->MultiCell(165,5, Texto::encodeLatin1( $parametroExamen->referencia ),$this->debug ,'J', 0 );
                        $this->ln();
						
						if( strlen ( Texto::trim($parametro->limite_deteccion) ) > 0 ){
							$this->SetX($this->line_begin+10);
							$this->SetFont($this->font,'B',$this->font_body_size);
							$this->Cell(70,5, Texto::encodeLatin1( 'Límite de detección:'),$this->debug, 0,'L',0);					
							$this->SetFont($this->font,'',$this->font_body_size);						
							$this->Cell(95,5, Texto::encodeLatin1( $parametro->limite_deteccion  ),$this->debug, 0,'L',0);
							$this->ln();
						}
                        
                        $this->SetX($this->line_begin+10);
						$this->SetFont($this->font,'B',$this->font_body_size);
                        $this->Cell(70,5, Texto::encodeLatin1( 'Fecha y hora de emisión del informe de resultados:'),$this->debug, 0,'L',0);
						$date = new \DateTime($this->orden->fecha_resultados );
                        $time = new \DateTime('2021-01-01 '.$this->orden->hora_resultados ); 
                        $this->SetFont($this->font,'',$this->font_body_size);						
                        $this->Cell(95,5, Texto::encodeLatin1(   $this->generateEnLetras(date_format($date, 'Y/m/d')) . ' PDF_ORDEN_ACCESS.php' .  date_format($time, 'H\Hi')   ),$this->debug, 0,'L',0);
                        $this->ln(30);
                        
                        $y= $this->GetY();
                        $this->SetFont($this->font,'',$this->font_body_size);                       
                        if( !empty($this->orden->laboratorista_id ) && $this->orden->laboratorista_id <> 2 ){
                            $laboratorista = $this->laboratorista;
                            $this->Cell(90,3,Texto::encodeLatin1( $laboratorista->nombres), $this->debug ,0,'C');
                            $this->Ln();
							$this->Cell(90,3,Texto::encodeLatin1('Rg. ACESS. '. $laboratorista->identificacion), $this->debug,0,'C');
                            $this->Ln(); 
                            $this->SetFont($this->font,'B',$this->font_body_size);
                            $this->SetX(30);
                            $this->MultiCell(50,3,Texto::encodeLatin1('Responsable de la emisión de los resultados de la prueba'), $this->debug,'C');
							 if( $this->digital_sign){
                                 if( strlen($laboratorista->firma_digital_fullname) > 0) {
                                     $url = Texto::encodeLatin1("FIRMADO POR: " . $laboratorista->firma_digital_fullname .
                                         chr(10) . "FECHA: " . $this->orden->fecha_firmado_digital);
                                     $qrCode = QrCode::create($url);
                                     $qrCode->setSize(250)->setMargin(5)->setForegroundColor(new Color(55, 55, 55));

                                     $writer = new PngWriter();
                                     $result = $writer->write($qrCode);
                                     $dataUri = $result->getDataUri();

                                     $this->Image($dataUri, $this->line_begin + 5, $y - 20, 20, 20, 'png');
                                     $this->SetXY($this->line_begin + 25, $y - 16);
                                     $this->SetFont($this->font_sign, '', $this->font_body_size);
                                     $this->Cell(90, 1, Texto::encodeLatin1("Firmado electrónicamente por:"), $this->debug, 0, 'J');
                                     $this->ln(1);
                                     $this->SetFont($this->font_sign, 'B', $this->font_title_size);
                                     $this->SetXY($this->line_begin + 25, $y - 14);
                                     $this->MultiCell(105, 4, Texto::encodeLatin1($this->subjectSignature($laboratorista->firma_digital_fullname)), $this->debug, 'J', 0);
                                 }
							}else{
							$urlImage = __DIR__ . '/ziehllab/' .$laboratorista->dir_imagen_firma;
							if (@getimagesize($urlImage)) {
								$this->Image( $urlImage, $this->GetX()+17, $this->GetY() - 34,60,  30,  pathinfo( $urlImage, PATHINFO_EXTENSION ) );
								}
							}						
                            
                        }
                        
                        $this->SetY($y);
                        $this->SetFont($this->font,'',$this->font_body_size); 
                        $responsableTecnico = $this->responsableTecnico;
                        if( !is_null($responsableTecnico)){                          
                            $this->SetX(100);
                            $this->Cell(90,3,Texto::encodeLatin1( $responsableTecnico->nombres), $this->debug ,0,'C');                            
                            $this->Ln();
                            $this->SetX(100);
							 $this->Cell(90,3,Texto::encodeLatin1('Rg. ACESS. '.$responsableTecnico->identificacion), $this->debug,0,'C');
                            $this->Ln();
                            $this->SetX(120);
                            $this->SetFont($this->font,'B',$this->font_body_size);
                            $this->MultiCell(50,3,Texto::encodeLatin1('Firma del profesional que valida la prueba'), $this->debug,'C');
							   if( $this->digital_sign){
                                   if( strlen($responsableTecnico->firma_digital_fullname) > 0) {
                                       $url = Texto::encodeLatin1("FIRMADO POR: " . $responsableTecnico->firma_digital_fullname .
                                           chr(10) . "FECHA: " . $this->orden->fecha_firmado_digital);
                                       $qrCode = QrCode::create($url);
                                       $qrCode->setSize(250)->setMargin(5)->setForegroundColor(new Color(55, 55, 55));

                                       $writer = new PngWriter();
                                       $result = $writer->write($qrCode);
                                       $dataUri = $result->getDataUri();
                                       $this->Image($dataUri, 120, $y - 20, 20, 20, 'png');

                                       // 6. Convertir el resultado a base64 (data URI)

                                       $this->SetXY(140, $y - 16);
                                       $this->SetFont($this->font_sign, '', $this->font_body_size);
                                       $this->Cell(90, 1, Texto::encodeLatin1("Firmado electrónicamente por:"), $this->debug, 0, 'J');
                                       $this->ln(1);
                                       $this->SetFont($this->font_sign, 'B', $this->font_title_size);
                                       $this->SetXY(140, $y - 14);
                                       $this->MultiCell(105, 4, Texto::encodeLatin1($this->subjectSignature($responsableTecnico->firma_digital_fullname)), $this->debug, 'J', 0);
                                   }
								}
								else {
									$this->SetX(100);
									$urlImage = __DIR__ . '/ziehllab/' .$responsableTecnico->dir_imagen_firma;
									if (@getimagesize($urlImage)) {
										$this->Image( $urlImage, $this->GetX()+17, $this->GetY() - 34,60,  30,  pathinfo( $urlImage, PATHINFO_EXTENSION ) );
									}
								}
							
							
                           
                        }

                        $this->ln();

                        $y = 210;
                            $this->setY($y );


                        $data = (strlen($this->orden->token) > 50)
                            ? 'https://' . $_SERVER['HTTP_HOST'] . '/documentos/analisis?token=' . $this->orden->token
                            : $this->orden->codigo;

                        $qrCode = QrCode::create($data);
                        $qrCode->setSize(250)
                            ->setMargin(5)
                            ->setForegroundColor(new Color(55, 55, 55))
                            ->setBackgroundColor(new Color(255, 255, 255));

                        $logo = Logo::create(__DIR__ . '/../../../media/imagen/app/ziehllab_logo.png') // Asegúrate que esta ruta esté correcta
                        ->setResizeToWidth(150)
                            ->setResizeToHeight(60);

                        // 5. Generar el QR con logo y texto usando PngWriter
                        $writer = new PngWriter();
                        $result = $writer->write($qrCode, $logo);

                        // 6. Convertir el resultado a base64 (data URI)
                        $dataUri = $result->getDataUri();

                        $this->Image($dataUri,$this->line_begin,$y+20,20,20,'png');
                        $y = $y+40;
                        $this->SetXY($this->line_begin + 20,$y-16);
                        $this->Cell(90,1,Texto::encodeLatin1("ziehllab.com"), $this->debug ,0,'J');
                        $this->ln(1);
                        $this->SetFont($this->font,'',$this->font_body_size);
                        $this->SetXY($this->line_begin + 20,$y-14);
                        $this->MultiCell(105,4,Texto::encodeLatin1('Escanea este codigo QR'. chr(10).'para visualizar el documento  digital'),$this->debug ,'J',0);

                        $this->ln(100);
                    }
                    
                }
               
            }
		
            
    }
    
        

    
    public function analisis(){
        
        $this->SetX($this->line_begin);        
        $this->SetFont($this->font,'BU',$this->font_title_size);        
        if(!empty($this->analisis->descripcion)){
            $this->Cell(195,5, Texto::encodeLatin1($this->analisis->descripcion.' ('.$this->analisis->nombre.')'),0,0,'L');
        } else{$this->Cell(195,5, Texto::encodeLatin1($this->analisis->nombre),0,0,'L');}
        
        $this->ln();
        $this->getImpresionParametros();
        $this->ln();
        
        
    }
    
    
    
    public function textoNota($nota){
        if(isset($nota) && Texto::trim($nota)!=''){
                $this->SetX(30);
                $this->SetFont($this->font,'B',$this->font_body_size);
               
                $this->Cell(15,5,Texto::encodeLatin1( 'NOTA:' ),0,0,'L');
                $this->SetFont($this->font,'',$this->font_body_size);
                $this->MultiCell(148,5, Texto::encodeLatin1( $nota ),1,'J',0);
                $this->ln();
        }
        
    }
    private function impresionPanel(){
        if( $this->examen['analisis_id']== 176 ){
            return true;
        }

        return false;


    }
    private function impresionAccessLab(){
		if( $this->examen['analisis_id']== 228 ){
            return $this->antiSarsCov2;
        }
	  
        if( $this->examen['analisis_id']== 218 ){
            return $this->rapidTestIgg;
        }
		 if( $this->examen['analisis_id']== 219 ){
            return $this->rapidTestIgm;
        }
        
        if($this->examen['analisis_id'] == 237){ 
            return $this->nCovIggClia;
        }
		if($this->examen['analisis_id'] == 238){ 
            return $this->nCovIgmClia;
        }
		
		if($this->examen['analisis_id'] == 265){ 
            return $this->hisopadoAntigeno;
        }
		
		if($this->examen['analisis_id'] == 313){ 
            return $this->sarsCov2SRbdIgg;
        }
		
		if($this->examen['analisis_id'] == 279){ 
            return $this->rTPCRtiempoReal;
        }
		
		
        return false;
        
        
    }
    
    public function germenes(){
        foreach ($this->examen->examenGermenes as $examenGermen) {            
            $this->SetX($this->line_begin+5);
            if(isset($examenGermen->germen_id)){
                $this->Cell(50,5,Texto::encodeLatin1('IDENTIFICACION DEL GERMEN'),0,0,'L');
                $this->SetFont($this->font,'B',$this->font_body_size);
                $this->Cell(65,5,Texto::encodeLatin1( isset($examenGermen->germen_id) ? $examenGermen->germen->descripcion :'' ),0,0,'R');
            }
            $this->SetFont($this->font,'',$this->font_body_size);
            $this->ln();
            
            if( strlen( Texto::trim($examenGermen->contaje_colonia ) ) >0 ){
            $this->SetX($this->line_begin+5);
            $this->Cell(50,5,Texto::encodeLatin1('CONTAJE BACTERIANO'),0,0,'L');
            $this->SetFont($this->font,'B',$this->font_body_size);
            $this->Cell(65,5,Texto::encodeLatin1($examenGermen->contaje_colonia.' [UFC/ML]'),0,0,'R');
            $this->SetFont($this->font,'',$this->font_body_size);
            $this->ln();
            $this->ln();
            }else {
                $this->ln();
            }
            
			$tablaSensibilidad = $examenGermen->getExamenGermenAntibioticos()->joinWith(
                ['antibiotico'])
                ->select(
                    [   'antibiotico.id',
                        'antibiotico.descripcion',
                        'tipo',
                        'valor',
                    ])
                    ->orderBy(['tipo'=>SORT_DESC,'antibiotico.descripcion'=>SORT_ASC])->All();
			
            if( count( $tablaSensibilidad ) > 0 ){
                    $this->SetX($this->line_begin+50);
                    $this->SetFont($this->font,'B',$this->font_body_size);
                    $this->Cell(36,5,Texto::encodeLatin1('AGENTE MICROBIANO'),'TB',0,'R');
                    $this->Cell(36,5,Texto::encodeLatin1('INHIBICION EN [MM]'),'TB',0,'R');
                    $this->Cell(55,5,Texto::encodeLatin1('DIAMETRO DE ZONA'),'TB',0,'R');
                    $this->ln(); 
            }
          
			foreach( $tablaSensibilidad as $agentes ){				
					$this->SetX($this->line_begin+50);
					$this->SetFont($this->font,'',$this->font_body_size);  
					$this->Cell(36,5,Texto::encodeLatin1($agentes->descripcion),'TB',0,'R');
					$this->Cell(36,5,Texto::encodeLatin1($agentes->valor.' [mm]'),'TB',0,'R');
					$this->Cell(55,5,Texto::encodeLatin1($agentes->tipo),'TB',0,'R');
					$this->ln();  
				}
            
            
            $this->ln();
        
        }
        
    }
    

   public function QrCode()
    {
        // La data que irá en el QR
        $data = '';

        if (strlen($this->orden->token) > 50) {
            $data = 'https://' . $_SERVER['HTTP_HOST'] . '/documentos/analisis?token=' . $this->orden->token;
        } else {
            $data = $this->orden->codigo;
        }

        // 1. Instanciar el objeto QrCode y establecer los datos
        $qrCode = QrCode::create($data)
            ->setSize(250)
            ->setMargin(5);
        $writer = new PngWriter();

        // 4. Escribir el QR y obtener el Data URI
        $result = $writer->write($qrCode);
        $dataUri = $result->getDataUri();

        // 5. Insertar la imagen en FPDF usando el Data URI
        $this->Image($dataUri, 164, 45, 29, 29, 'png');
    }

    public function Header(){
        if( !$this->impresionAccessLab() or ! $this->impresionPanel()){
		$this->SetDrawColor( 0, 0, 0 );
        $this->Image(__DIR__ . '/../../../media/imagen/app/a4.png',0,0,210,297,'png');
        $this->SetXY($this->line_begin,46);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(146,5,Texto::encodeLatin1('DATOS DE LA ORDEN'),'BT',0,'L');
        $this->ln();
        
        $this->SetX($this->line_begin);
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(30,5,Texto::encodeLatin1('CODIGO:'),0,0,'L');
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(20,5,Texto::encodeLatin1($this->orden->codigo),0,0,'L');
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(50,5,Texto::encodeLatin1('IDENTIFICACIÓN DEL PACIENTE:'),0,0,'L');
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(25,5,Texto::encodeLatin1( strlen( $this->paciente->identificacion ) > 2 ? $this->paciente->identificacion:'' ),0,0,'L');
        $this->ln();           
        
        $this->SetX($this->line_begin);
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(30,5,Texto::encodeLatin1('PACIENTE:'),0,0,'L');
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(150,5,Texto::encodeLatin1($this->paciente->nombres),0,0,'L');
        $this->ln();
        

        $this->SetX($this->line_begin);
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(32,5,Texto::encodeLatin1('EDAD DEL PACIENTE:'),0,0,'L');
        $this->SetFont($this->font,'B',$this->font_body_size);
		$edad = $this->paciente->getEdad() ?? '---';
        $this->Cell(10,5,Texto::encodeLatin1($edad),0,0,'L');
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->ln();
        //}
        
        $this->SetX($this->line_begin);
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(30,5,Texto::encodeLatin1('DOCTOR:'),0,0,'L');
        $this->SetFont($this->font,'B',$this->font_body_size);
       
        if(isset($this->doctor)){ $this->Cell(150,5,Texto::encodeLatin1($this->doctor->nombres),0,0,'L'); }
        else{ $this->Cell(150,5,Texto::encodeLatin1(''),0,0,'L');}
       
        $this->ln();
        
        $this->SetX($this->line_begin);  
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(30,5,Texto::encodeLatin1('FECHA:'),0,0,'L');
        $this->SetFont($this->font,'B',$this->font_body_size);
        $date = new \DateTime($this->orden->fecha);
        $this->Cell(150,5,Texto::encodeLatin1('BABAHOYO, '. date_format($date, 'd/m/Y')),0,0,'L');
        $this->ln();        
        $this->tableHead();
        $this->QrCode();

        }
        
        
    }
    
    public function Footer()
    {
       
        if($this->footer ){
       // Go to 1.5 cm from bottom
	    $this->SetDrawColor( 0, 0, 0 );
        $this->SetY(-45);		
        $this->ln(15);
        $this->SetFont($this->font,'',$this->font_body_size);      
        $this->SetX($this->line_begin);

        if( !empty($this->orden->laboratorista_id)) {
            $y = $this->GetY();
            $laboratorista = $this->orden->laboratorista;
            $this->Cell(140, 3, Texto::encodeLatin1($laboratorista->nombres), 0, 0, 'C');
            $this->Ln();
            $this->SetX($this->line_begin);
            $this->Cell(140, 3, Texto::encodeLatin1('Rg. ACESS. ' . $laboratorista->identificacion), 0, 0, 'C');
            $this->SetFont($this->font, 'B', $this->font_body_size);
            $this->Ln();
            $this->SetX($this->line_begin);
            $this->Cell(140, 3, Texto::encodeLatin1($laboratorista->cargo), 0, 0, 'C');
        if($this->digital_sign){
            if( strlen($laboratorista->firma_digital_fullname) > 0) {
                $url = Texto::encodeLatin1("FIRMADO POR: " . $laboratorista->firma_digital_fullname .
                    chr(10) . "FECHA: " . $this->orden->fecha_firmado_digital);
                $qrCode = QrCode::create($url);
                $qrCode->setSize(250)->setMargin(5)->setForegroundColor(new Color(55, 55, 55));

                $writer = new PngWriter();
                $result = $writer->write($qrCode);
                $dataUri = $result->getDataUri();
                $this->Image($dataUri, $this->line_begin + 40, $y - 20, 20, 20, 'png');
                $this->SetXY($this->line_begin + 60, $y - 16);
                $this->SetFont($this->font_sign, '', $this->font_body_size);
                $this->Cell(90, 1, Texto::encodeLatin1("Firmado electrónicamente por:"), $this->debug, 0, 'J');
                $this->ln(1);
                $this->SetFont($this->font_sign, 'B', $this->font_title_size);
                $this->SetXY($this->line_begin + 60, $y - 14);
                $this->MultiCell(105, 4, Texto::encodeLatin1($this->subjectSignature($laboratorista->firma_digital_fullname)), $this->debug, 'J', 0);
            }
        }else {
            $urlImage =  __DIR__ . '/ziehllab/' .$laboratorista->dir_imagen_firma;;
            if (@getimagesize($urlImage)) {
                $this->Image($urlImage, $this->line_begin + 45, $this->GetY() - 32, 60, 30, pathinfo($urlImage, PATHINFO_EXTENSION));
            }

        }
        }
		
        $this->SetXY(165,288);
        $this->Cell(0,10,'Pagina '.$this->PageNo().'',0,0,'C');
        }
    }

    private function subjectSignature($subject)
    {
        $words = explode(" ",$subject);
        $fullname='';
        $i=0;
        foreach ( $words as $word){
            $fullname .= $word." ";
            $i++;
            if($i >1){
                $fullname .= chr(10);
                $i=0;
            }
        }
        return $fullname;
    }
    
    public function tableHead(){             
        $this->SetX($this->line_begin);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(65,5,Texto::encodeLatin1('EXAMEN REALIZADO'),'TB',0,'L');
        $this->Cell(38,5,Texto::encodeLatin1('RESULTADO'),'TB',0,'L');
        $this->Cell(45,5,Texto::encodeLatin1('UNIDADES'),'TB',0,'L');
        $this->Cell(30,5,Texto::encodeLatin1('VALORES DE REFERENCIA'),'TB',0,'R');
        $this->ln();
    }
    

    
   
    public function getImpresionParametros(){
            
            $parametros=$this->examen->analisis->parametrosParaIngresoResultado;
            $seccion='a';
            foreach ($parametros as $parametro) {
                $this->checkY();
                $this->SetX($this->line_begin+5);
                if(isset($parametro->seccion_id) && $seccion != $parametro->seccion_id ){
                    $this->SetFont($this->font,'B',$this->font_body_size);
                    $this->Cell(195,5, Texto::encodeLatin1($parametro->seccion->descripcion),0,0,'L');
                    $this->ln();
                    
                }  
                $this->checkY();
                $this->SetFont($this->font,'',$this->font_body_size);          
                $parametroExamen=$parametro->getExamenParametros()->where([ 'examen_id' => $this->examen->id, 'parametro_id' => $parametro->id ])->One();
                                
                if(isset($parametroExamen)){  
                    
                     if( !empty( $parametroExamen->valor ) or Texto::trim($parametroExamen->valor) != '' ){ 
                         $this->checkY();
                         if(isset($parametro->metodo_id)){
                             $this->SetX($this->line_begin+5);
                             $y=$this->y;
                             $this->SetFont($this->font,'B',$this->font_body_size);
                             $this->MultiCell(85,5, Texto::encodeLatin1('METODO'),0,'L',0);
                             $this->SetXY(100,$y);
                             $this->MultiCell(95,5, Texto::encodeLatin1($parametro->metodo->descripcion),0,'L',0);
                             $this->SetFont($this->font,'',$this->font_body_size);
                         }
                         $this->checkY();
                        $referencia = $parametroExamen->referencia;
						strlen( Texto::trim( $parametroExamen->referencia ) ) == 0 ? $anchoCeldaReferencia = 0:  $anchoCeldaReferencia = 60;
                        $this->SetX($this->line_begin+5);
                        $y=$this->y;         
                        $this->MultiCell(55,5, Texto::encodeLatin1( $parametroExamen->descripcion ),0 ,'L', 0 );            
                        $anchoCeldaValor=30;
                        $anchoCeldaMedida=35;
                        if( strlen( Texto::trim( $parametroExamen->medida ) ) == 0 && strlen( Texto::trim( $parametroExamen->valor ) ) > 10){ 
                            $anchoCeldaValor=85;
							strlen( Texto::trim( $parametroExamen->referencia ) ) == 0 ?$anchoCeldaValor+= 37:'';
                            $anchoCeldaMedida=0;
                            $this->SetXY(70,$y);
                            $this->MultiCell($anchoCeldaValor,5, Texto::encodeLatin1( Texto::trim($parametroExamen->valor )),0 ,'L', 0 );
                        }else{
                            $this->SetXY(70,$y);
                            $this->MultiCell(30,5, Texto::encodeLatin1( Texto::trim($parametroExamen->valor )),0 ,'R', 0 );
                            $this->SetXY(100,$y);
                            $this->MultiCell($anchoCeldaMedida,5, Texto::encodeLatin1( $parametroExamen->medida ),0 ,'R', 0 );
                        }
                        
                       
                        $this->SetXY(135,$y);
                        $this->MultiCell( $anchoCeldaReferencia,5, Texto::encodeLatin1( $referencia ),0 ,'R', 0 );
                        
                        if( $this->GetStringWidth($parametro->descripcion) > 55 || $this->GetStringWidth($parametroExamen->valor) > $anchoCeldaValor || $this->GetStringWidth($parametroExamen->medida) > $anchoCeldaMedida ||  $this->GetStringWidth( $referencia ) > 60 ){
                           $this->ln();                
                        }
                      
               
                    }
                         
                }
            $seccion = $parametro->seccion_id;
         }
               
           
        
        
    }

    private function checkY(){
        if($this->y > 240 ){
            $this->AddPage('P');
        }
        
    }
	
	 private function meses()
    {
        return  [
            1 => 'enero',
            2 => 'febrero',
            3 => 'marzo',
            4 => 'abril',
            5 => 'mayo',
            6 => 'junio',
            7 => 'julio',
            8 => 'agosto',
            9 => 'septiembre',
            10 => 'octubre',
            11 => 'noviembre',
            12 => 'diciembre'
        ];
    }
	
	 private function generateEnLetras($fecha){
        $cadena='';
        if(isset($fecha)){
            $fecha=explode("/",  $fecha);
        $dia=intval($fecha[2],10);
        $mes=intval($fecha[1],10);
        $anio=intval($fecha[0],10);       
        $array_meses=$this->meses();
        
        
    $cadena=  $dia;      
        $cadena.= ' de ';
        $cadena.=$array_meses[$mes];
        $cadena.= ' del '.$anio;
               
        
        }
        return $cadena;
    }
    
    /**Texto ajustado ene celda***/
    //Cell with horizontal scaling if text is too wide
    function CellFit($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='', $scale=false, $force=true)
    {
        //Get string width
        $str_width=$this->GetStringWidth($txt);
        
        //Calculate ratio to fit cell
        if($w==0)
            $w = $this->w-$this->rMargin-$this->x;
            $ratio = ($w-$this->cMargin*2)/$str_width;
            
            $fit = ($ratio < 1 || ($ratio > 1 && $force));
            if ($fit)
            {
                if ($scale)
                {
                    //Calculate horizontal scaling
                    $horiz_scale=$ratio*100.0;
                    //Set horizontal scaling
                    $this->_out(sprintf('BT %.2F Tz ET',$horiz_scale));
                }
                else
                {
                    //Calculate character spacing in points
                    $char_space=($w-$this->cMargin*2-$str_width)/max($this->MBGetStringLength($txt)-1,1)*$this->k;
                    //Set character spacing
                    $this->_out(sprintf('BT %.2F Tc ET',$char_space));
                }
                //Override user alignment (since text will fill up cell)
                $align='';
            }
            
            //Pass on to Cell method
            $this->Cell($w,$h,$txt,$border,$ln,$align,$fill,$link);
            
            //Reset character spacing/horizontal scaling
            if ($fit)
                $this->_out('BT '.($scale ? '100 Tz' : '0 Tc').' ET');
    }
    
    //Cell with horizontal scaling only if necessary
    function CellFitScale($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        $this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,true,false);
    }
    
    //Cell with horizontal scaling always
    function CellFitScaleForce($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        $this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,true,true);
    }
    
    //Cell with character spacing only if necessary
    function CellFitSpace($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        $this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,false,false);
    }
    
    //Cell with character spacing always
    function CellFitSpaceForce($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        //Same as calling CellFit directly
        $this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,false,true);
    }
    
    //Patch to also work with CJK double-byte text
    function MBGetStringLength($s)
    {
        if($this->CurrentFont['type']=='Type0')
        {
            $len = 0;
            $nbbytes = strlen($s);
            for ($i = 0; $i < $nbbytes; $i++)
            {
                if (ord($s[$i])<128)
                    $len++;
                    else
                    {
                        $len++;
                        $i++;
                    }
            }
            return $len;
        }
        else
            return strlen($s);
    }
    

    
/* Celda justificada**/
    function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        $k=$this->k;
        if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak())
        {
            $x=$this->x;
            $ws=$this->ws;
            if($ws>0)
            {
                $this->ws=0;
                $this->_out('0 Tw');
            }
            $this->AddPage($this->CurOrientation);
            $this->x=$x;
            if($ws>0)
            {
                $this->ws=$ws;
                $this->_out(sprintf('%.3F Tw',$ws*$k));
            }
        }
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
            $s='';
            if($fill || $border==1)
            {
                if($fill)
                    $op=($border==1) ? 'B' : 'f';
                    else
                        $op='S';
                        $s=sprintf('%.2F %.2F %.2F %.2F re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
            }
            if(is_string($border))
            {
                $x=$this->x;
                $y=$this->y;
                if(is_int(strpos($border,'L')))
                    $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
                    if(is_int(strpos($border,'T')))
                        $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
                        if(is_int(strpos($border,'R')))
                            $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
                            if(is_int(strpos($border,'B')))
                                $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
            }
            if($txt!='')
            {
                if($align=='R')
                    $dx=$w-$this->cMargin-$this->GetStringWidth($txt);
                    elseif($align=='C')
                    $dx=($w-$this->GetStringWidth($txt))/2;
                    elseif($align=='FJ')
                    {
                        //Set word spacing
                        $wmax=($w-2*$this->cMargin);
                        $this->ws=($wmax-$this->GetStringWidth($txt))/substr_count($txt,' ');
                        $this->_out(sprintf('%.3F Tw',$this->ws*$this->k));
                        $dx=$this->cMargin;
                    }
                    else
                        $dx=$this->cMargin;
                        $txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
                        if($this->ColorFlag)
                            $s.='q '.$this->TextColor.' ';
                            $s.=sprintf('BT %.2F %.2F Td (%s) Tj ET',($this->x+$dx)*$k,($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,$txt);
                            if($this->underline)
                                $s.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
                                if($this->ColorFlag)
                                    $s.=' Q';
                                    if($link)
                                    {
                                        if($align=='FJ')
                                            $wlink=$wmax;
                                            else
                                                $wlink=$this->GetStringWidth($txt);
                                                $this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$wlink,$this->FontSize,$link);
                                    }
            }
            if($s)
                $this->_out($s);
                if($align=='FJ')
                {
                    //Remove word spacing
                    $this->_out('0 Tw');
                    $this->ws=0;
                }
                $this->lasth=$h;
                if($ln>0)
                {
                    $this->y+=$h;
                    if($ln==1)
                        $this->x=$this->lMargin;
                }
                else
                    $this->x+=$w;
    }

    

        
    }