<?php
namespace app\modules\lab\pdfs;

use Da\QrCode\QrCode;
use inquid\pdf\FPDF;




class PDF_ORDEN extends FPDF
{

    public $examen;
    public $orden;
    public $paciente;
    public $doctor;
    public $analisis;
    
    public $secciones;
    public $font;
    public $font_title_size;
    public $font_body_size;
    public $font_body_table_title;    
    public $line_begin;    
    
    
    public function __construct($orden_id){

      
        $this->orden=Orden::findOne($orden_id);    
        $this->paciente=$this->orden->paciente;
        $this->doctor=$this->orden->doctor;   
       
        $this->font='Arial';
        $this->font_title_size=12;        
        $this->font_body_size=8;
        
        $this->line_begin=15;
        parent::__construct();
        $this->AddPage('P','A4');            
        $this->SetAutoPageBreak(true,50);      
        $this->ln(); 
        $numero_hoja_men=0;
        foreach ($this->orden->examenesParaImprimir as $examen) {           
          $this->examen = $examen;
          $this->analisis = $this->examen->analisis;
          $numero_hoja=$this->analisis->hoja_impresion;         
          if($numero_hoja!= $numero_hoja_men && 0 !=  $numero_hoja_men){  $this->AddPage('P','A4'); }         
          $this->Body();
          $this->germenes();            
          $numero_hoja_men=$this->analisis->hoja_impresion;
          $this->textoNota( $examen->nota );
        }
        
       
    }
    
    public function Body(){
        
        $this->SetX($this->line_begin);        
        $this->SetFont($this->font,'BU',$this->font_title_size);        
        if(!empty($this->analisis->descripcion)){
            $this->Cell(195,5, utf8_decode($this->analisis->descripcion.' ('.$this->analisis->nombre.')'),0,0,'L');
        } else{$this->Cell(195,5, utf8_decode($this->analisis->nombre),0,0,'L');}
        
        $this->ln();
        $this->getImpresionParametros();
        $this->ln();
        
        
    }
    
    
    
    public function textoNota($nota){
        if(isset($nota) && trim($nota)!=''){
                $this->SetX(30);
                $this->SetFont($this->font,'B',$this->font_body_size);
               
                $this->Cell(15,5,utf8_decode( 'NOTA:' ),0,0,'L');
                $this->SetFont($this->font,'',$this->font_body_size);
                $this->MultiCell(148,5, utf8_decode( $nota ),1,'J',0);
                $this->ln();
        }
        
    }
    
    public function germenes(){
        foreach ($this->examen->examenGermenes as $examenGermen) {            
            $this->SetX($this->line_begin+5);
            if(isset($examenGermen->germen_id)){
                $this->Cell(50,5,utf8_decode('IDENTIFICACION DEL GERMEN'),0,0,'L');
                $this->SetFont($this->font,'B',$this->font_body_size);
                $this->Cell(65,5,utf8_decode( isset($examenGermen->germen_id) ? $examenGermen->germen->descripcion :'' ),0,0,'R');
            }
            $this->SetFont($this->font,'',$this->font_body_size);
            $this->ln();
            
            if( strlen( trim($examenGermen->contaje_colonia ) ) >0 ){
            $this->SetX($this->line_begin+5);
            $this->Cell(50,5,utf8_decode('CONTAJE BACTERIANO'),0,0,'L');
            $this->SetFont($this->font,'B',$this->font_body_size);
            $this->Cell(65,5,utf8_decode($examenGermen->contaje_colonia.' [UFC/ML]'),0,0,'R');
            $this->SetFont($this->font,'',$this->font_body_size);
            $this->ln();
            $this->ln();
            }else {
                $this->ln();
            }
            
            if(isset($examenGermen->germen_id)){
                    $this->SetX($this->line_begin+50);
                    $this->SetFont($this->font,'B',$this->font_body_size);
                    $this->Cell(36,5,utf8_decode('AGENTE MICROBIANO'),'TB',0,'R');
                    $this->Cell(36,5,utf8_decode('INHIBICION EN [MM]'),'TB',0,'R');
                    $this->Cell(55,5,utf8_decode('DIAMETRO DE ZONA'),'TB',0,'R');
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
            
                  foreach ($tablaSensibilidad as $agentes) {
                        
                        $this->SetX($this->line_begin+50);
                        $this->SetFont($this->font,'',$this->font_body_size);  
                        $this->Cell(36,5,utf8_decode($agentes->descripcion),'TB',0,'R');
                        $this->Cell(36,5,utf8_decode($agentes->valor.' [mm]'),'TB',0,'R');
                        $this->Cell(55,5,utf8_decode($agentes->tipo),'TB',0,'R');
                        $this->ln();  
                                            
                ;
            }
            
            
            $this->ln();
        
        }
        
    }
    

  public function QrCode(){
        
       
      if( strlen($this->orden->token)>50 ){
          $url = 'https://'.$_SERVER['HTTP_HOST'].'/documentos/analisis?token='.$this->orden->token;
          $qrCode = (new QrCode($url));
      }
      else{  $qrCode = (new QrCode($this->orden->codigo)); }
       
      $qrCode->setSize(250)
        ->setMargin(5)
        ->useForegroundColor(55, 55, 55);        
        $this->Image($qrCode->writeDataUri(),164,45,29,29,'png');
       
    }
   
    public function Header(){
        $this->Image(__DIR__.'/../imagen/a4.png',0,0,210,297,'png');
        $this->SetXY($this->line_begin,46);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(146,5,utf8_decode('DATOS DE LA ORDEN'),'BT',0,'L');
        $this->ln();
        
        $this->SetX($this->line_begin);
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(30,5,utf8_decode('CODIGO:'),0,0,'L');
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(20,5,utf8_decode($this->orden->codigo),0,0,'L');
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(50,5,utf8_decode('IDENTIFICACIÓN DEL PACIENTE:'),0,0,'L');
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(25,5,utf8_decode( strlen( $this->paciente->identificacion ) > 2 ? $this->paciente->identificacion:'' ),0,0,'L');
        $this->ln();           
        
        $this->SetX($this->line_begin);
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(30,5,utf8_decode('PACIENTE:'),0,0,'L');
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(150,5,utf8_decode($this->paciente->nombres),0,0,'L');
        $this->ln();
        
        if($this->paciente->unidad_tiempo=='RN'){
            $this->SetX($this->line_begin);
            $this->SetFont($this->font,'',$this->font_body_size);
            $this->Cell(32,5,utf8_decode('EDAD DEL PACIENTE:'),0,0,'L');
            $this->SetFont($this->font,'B',$this->font_body_size);
            $this->Cell(10,5,utf8_decode('RN'),0,0,'L');           
            $this->ln();
        }
        else{
        $this->SetX($this->line_begin);
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(32,5,utf8_decode('EDAD DEL PACIENTE:'),0,0,'L');
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(10,5,utf8_decode($this->paciente->edad),0,0,'L');
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(15,5,utf8_decode('EDAD EN :'),0,0,'L');
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(10,5,utf8_decode($this->paciente->unidad_tiempo),0,0,'L');
        $this->ln();
        }
        
        $this->SetX($this->line_begin);
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(30,5,utf8_decode('DOCTOR:'),0,0,'L');
        $this->SetFont($this->font,'B',$this->font_body_size);
       
        if(isset($this->doctor)){ $this->Cell(150,5,utf8_decode($this->doctor->nombres),0,0,'L'); }
        else{ $this->Cell(150,5,utf8_decode(''),0,0,'L');}
       
        $this->ln();
        
        $this->SetX($this->line_begin);  
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(30,5,utf8_decode('FECHA:'),0,0,'L');
        $this->SetFont($this->font,'B',$this->font_body_size);
        $date = new \DateTime($this->orden->fecha);
        $this->Cell(150,5,utf8_decode('BABAHOYO, '. date_format($date, 'd/m/Y')),0,0,'L');
        $this->ln();        
        $this->tableHead();
        $this->QrCode();

       
        
        
    }
    
    function Footer()
    {
        // Go to 1.5 cm from bottom
        $this->SetY(-45);
        $this->ln(15);
        $this->SetFont($this->font,'B',$this->font_body_size);      
        $this->SetX($this->line_begin);
        if( !empty($this->orden->laboratorista_id)){
            $laboratorista = $this->orden->laboratorista;
            $this->Cell(140,3,utf8_decode( $laboratorista->nombres),0,0,'C');
            $this->Ln();$this->SetX($this->line_begin);
            $this->Cell(140,3,utf8_decode( $laboratorista->cargo),0,0,'C');
            $this->Ln();$this->SetX($this->line_begin);
            $this->Cell(140,3,utf8_decode( $laboratorista->registro_msp),0,0,'C');
            $this->Ln();$this->SetX($this->line_begin);
            $this->Cell(140,3,utf8_decode( $laboratorista->registro_senescyt),0,0,'C');
            $this->Ln();
            $this->Line(50, 266, 120, 266);
        }else{
            $this->Cell(190,5,utf8_decode('RESPONSABLE'),0,0,'C');
            $this->Line(90, 267, 130, 267);
        }
       
   
        $this->SetXY(165,288);
        $this->Cell(0,10,'Pagina '.$this->PageNo().'',0,0,'C');
    }
    
    public function tableHead(){             
        $this->SetX($this->line_begin);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(65,5,utf8_decode('EXAMEN REALIZADO'),'TB',0,'L');
        $this->Cell(38,5,utf8_decode('RESULTADO'),'TB',0,'L');
        $this->Cell(45,5,utf8_decode('UNIDADES'),'TB',0,'L');
        $this->Cell(30,5,utf8_decode('VALORES DE REFERENCIA'),'TB',0,'R');
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
                    $this->Cell(195,5, utf8_decode($parametro->seccion->descripcion),0,0,'L');
                    $this->ln();
                    
                }  
                $this->checkY();
                $this->SetFont($this->font,'',$this->font_body_size);          
                $parametroExamen=$parametro->getExamenParametros()->where([ 'examen_id' => $this->examen->id, 'parametro_id' => $parametro->id ])->One();
                                
                if(isset($parametroExamen)){  
                    
                     if( !empty( $parametroExamen->valor ) or trim($parametroExamen->valor) != '' ){ 
                         $this->checkY();
                         if(isset($parametro->metodo_id)){
                             $this->SetX($this->line_begin+5);
                             $y=$this->y;
                             $this->SetFont($this->font,'B',$this->font_body_size);
                             $this->MultiCell(85,5, utf8_decode('METODO'),0,'L',0);
                             $this->SetXY(100,$y);
                             $this->MultiCell(95,5, utf8_decode($parametro->metodo->descripcion),0,'L',0);
                             $this->SetFont($this->font,'',$this->font_body_size);
                         }
                         $this->checkY();
                        $referencia = $parametroExamen->referencia;
                        $this->SetX($this->line_begin+5);
                        $y=$this->y;         
                        $this->MultiCell(55,5, utf8_decode( $parametroExamen->descripcion ),0 ,'L', 0 );            
                        $anchoCeldaValor=30;
                        $anchoCeldaMedida=35;
                        if( strlen( trim( $parametroExamen->medida ) ) == 0 && strlen( trim( $parametroExamen->valor ) ) > 10){ 
                            $anchoCeldaValor=85;
                            $anchoCeldaMedida=0;
                            $this->SetXY(70,$y);
                            $this->MultiCell($anchoCeldaValor,5, utf8_decode( trim($parametroExamen->valor )),0 ,'L', 0 );
                        }else{
                            $this->SetXY(70,$y);
                            $this->MultiCell(30,5, utf8_decode( trim($parametroExamen->valor )),0 ,'R', 0 );
                            $this->SetXY(100,$y);
                            $this->MultiCell($anchoCeldaMedida,5, utf8_decode( $parametroExamen->medida ),0 ,'R', 0 );
                        }
                        
                       
                        $this->SetXY(135,$y);
                        $this->MultiCell(60,5, utf8_decode( $referencia ),0 ,'R', 0 );
                        
                        if( $this->GetStringWidth($parametro->descripcion) > 55 || $this->GetStringWidth($parametroExamen->valor) > $anchoCeldaValor || $this->GetStringWidth($parametroExamen->medida) > $anchoCeldaMedida ||  $this->GetStringWidth( $referencia ) > 60 ){
                           $this->ln();                
                        }
                      
               
                    }
                         
                }
            $seccion = $parametro->seccion_id;
         }
               
           
        
        
    }

    public function checkY(){
        if($this->y > 240 ){
            $this->AddPage('P');
        }
        
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