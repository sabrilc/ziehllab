<?php
namespace app\models;

use yii\db\Query;
use components\PHPlot;
use components\PDF_MemImage;


class PDF_INFORME extends PDF_MemImage
{
    public $font;
    public $font_title_size;
    public $font_body_size;    
    public $line_begin;
    
    private $fecha_inicio;
    private $fecha_fin;
    
    public function __construct( $fecha_inicio , $fecha_fin ){
        $this->font='Arial';
        $this->font_title_size=12;
        $this->font_body_size=8;
        
        $this->line_begin=15;
        parent::__construct( );
        $this->fecha_inicio =$fecha_inicio;
        $this->fecha_fin = $fecha_fin;      
        $this->AddPage('P','A4');  
        $this->head();		
        $this->body();
     
   
      
    }
    


   
    public function head(){
        $this->SetFont($this->font,'B',$this->font_title_size);
        $this->SetX($this->line_begin);
        $this->Cell(180,5,utf8_decode('FECHAS DE CORTE DEL INFORME'),1,0,'C');       
        $this->ln();
        
        $this->SetX($this->line_begin);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(60,5,utf8_decode('FECHA INICIAL'),1 ,0, 'L');
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(30,5, $this->fecha_inicio, 1, 0,'R');
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(60,5,utf8_decode('FECHA FIN'),1 ,0, 'L');
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(30,5,$this->fecha_fin, 1, 0,'R');
        $this->ln();
 
    }
    
    public function body(){
      $this->Ln();
      $orden= Orden::find()->where(['between', 'fecha', $this->fecha_inicio, $this->fecha_fin])->orderBy(['id'=>SORT_ASC])->one();
      if( isset($orden))
      {
    	  $this->ordenInicial();
          $this->ordenesEnProceso();
          $this->ordenesFinalizadas();
          $this->ordenesTotales();
    	  $this->ln();
    	  $this->examenMasRealizados();
          
    	  $this->ordenFinal();
          $this->valorTotalSinDescuento();
          $this->valorDescuento();
          $this->valorTotal();
          $this->ln();
          $this->examenVolumenVenta();
          $this->ln();
          
          $this->SetFont($this->font,'B',$this->font_title_size);
          $this->SetX($this->line_begin);      
          $this->Cell(180,5,utf8_decode('HISTORICO ANUAL'),0 ,0 ,'C' );
         
          $this->setY(220);
          $this->graficoAnual(); 
		  $this->ln();
          $this->examenesRealizados(); 		  
      }
      else{
          $this->SetX($this->line_begin);
          $this->Cell(180,5,utf8_decode('NO SE HA REGISTRADO NINGUNA ORDEN EN ESTE CORTE'),0 ,0 ,'L' );
      }
  
        
    }
	
   private function ordenInicial(){        
        $orden= Orden::find()->where(['between', 'fecha', $this->fecha_inicio, $this->fecha_fin])->orderBy(['id'=>SORT_ASC])->one();        
        $this->SetX($this->line_begin);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(60,5,utf8_decode('ORDEN INICIAL'),1,0,'L');
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(30,5,$orden->codigo,1,0,'R');  
        $this->ln();       
    }
	
	
	
    private function ordenesEnProceso(){        
        $ordenesEnProceso= Orden::find()->where(['between', 'fecha', $this->fecha_inicio, $this->fecha_fin])->andWhere(['<>','cerrada',true])->count();        
        $this->SetX($this->line_begin);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(60,5,utf8_decode('ORDENES EN PROCESO'),1,0,'L');
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(30,5,$ordenesEnProceso,1,0,'R');  
        $this->ln();
    }
    
    private function ordenesFinalizadas(){
        $ordenesEnProceso= Orden::find()->where(['between', 'fecha', $this->fecha_inicio, $this->fecha_fin])->andWhere(['cerrada'=>true])->count();
        $this->SetX($this->line_begin);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(60,5,utf8_decode('ORDENES FINALIZADAS'),1,0,'L');
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(30,5,$ordenesEnProceso,1,0,'R');
        $this->ln();
    }
    private function ordenesTotales(){
        $ordenesEnProceso= Orden::find()->where(['between', 'fecha', $this->fecha_inicio, $this->fecha_fin])->count();
        $this->SetX($this->line_begin);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(60,5,utf8_decode('ORDENES TOTALES'),1,0,'L');
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(30,5,$ordenesEnProceso,1,0,'R');
        $this->ln();
    }
	
	private function examenMasRealizados(){
        $examenes= ( new Query())->select(['analisis.nombre','count(analisis.id) as cantidad'])
                                         ->from('orden')
                                         ->join('inner join','examen','orden_id =orden.id')
                                         ->join('inner join','analisis','analisis_id =analisis.id')                                         
                                         ->where(['between', 'fecha', $this->fecha_inicio, $this->fecha_fin])
                                         ->groupBy(['analisis.nombre'])
                                         ->orderBy(['cantidad'=>SORT_DESC])
                                         ->limit(10)
                                         ->all();
                                         //;
         $this->SetX($this->line_begin);
         $this->SetFont($this->font,'B',$this->font_body_size);
         $this->Cell(90,5,utf8_decode('ANALISIS MAS REALIZADOS'),1,0,'C');
         $this->ln();
         
         $this->SetX($this->line_begin);
         $this->SetFont($this->font,'B',$this->font_body_size);
         $this->Cell(60,5,utf8_decode('ANALISIS'),1,0,'L');
         $this->Cell(30,5, 'CANTIDAD' ,1,0,'R');
         $this->ln();
                                         
       foreach ($examenes as $examen) {
           $this->SetX($this->line_begin);
           $this->SetFont($this->font,'',$this->font_body_size);
           $this->Cell(60,5,utf8_decode( substr( $examen['nombre'],0 , 32) ),1,0,'L');
           $this->Cell(30,5, $examen['cantidad'] ,1,0,'R');
           $this->ln();
        }
       
    }
	
	private function ordenFinal(){        
        $orden= Orden::find()->where(['between', 'fecha', $this->fecha_inicio, $this->fecha_fin])->orderBy(['id'=>SORT_DESC])->one();        
        $this->SetXY(105,25);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(60,5,utf8_decode('ORDEN FINAL'),1,0,'L');
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(30,5,$orden->codigo,1,0,'R');  
        $this->ln();
    }
	
    private function valorTotalSinDescuento(){
        $ordenesEnProceso= Orden::find()->where(['between', 'fecha', $this->fecha_inicio, $this->fecha_fin])->sum('precio');
        $this->SetX(105);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(60,5,utf8_decode('SUBTOTAL'),1,0,'L');
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(30,5,$ordenesEnProceso,1,0,'R');
        $this->ln();
    }
    
    private function valorDescuento(){
        $ordenesEnProceso= Orden::find()->where(['between', 'fecha', $this->fecha_inicio, $this->fecha_fin])->sum('descuento');
        $this->SetX(105);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(60,5,utf8_decode('DESCUENTOS REALIZADOS'),1,0,'L');
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(30,5,$ordenesEnProceso,1,0,'R');
        $this->ln();
    }
    
    private function valorTotal(){
        $ordenesEnProceso= Orden::find()->where(['between', 'fecha', $this->fecha_inicio, $this->fecha_fin])->sum('valor_total');
        $this->SetX(105);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(60,5,utf8_decode('VALOR TOTAL'),1,0,'L');
        $this->SetFont($this->font,'',$this->font_body_size);
        $this->Cell(30,5,$ordenesEnProceso,1,0,'R');
        $this->ln();
    }
    
    private function examenVolumenVenta(){
        $examenes= ( new Query())->select(['analisis.nombre','sum(analisis.precio) as valor'])
        ->from('orden')
        ->join('inner join','examen','orden_id =orden.id')
        ->join('inner join','analisis','analisis_id =analisis.id')
        ->where(['between', 'fecha', $this->fecha_inicio, $this->fecha_fin])
        ->groupBy(['analisis.nombre'])
        ->orderBy(['valor'=>SORT_DESC])
        ->limit(10)
        ->all();
        //;
        $this->SetX(105);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(90,5,utf8_decode('ANALISIS QUE GENERAN MAS INGRESOS'),1,0,'C');
        $this->ln();
        
        $this->SetX(105);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(60,5,utf8_decode('ANALISIS'),1,0,'L');
        $this->Cell(30,5, 'VENTA ($)' ,1,0,'R');
        $this->ln();
        
        foreach ($examenes as $examen) {
            $this->SetX(105);
            $this->SetFont($this->font,'',$this->font_body_size);
            $this->Cell(60,5,utf8_decode( substr( $examen['nombre'],0 , 32) ),1,0,'L');
            $this->Cell(30,5, $examen['valor'] ,1,0,'R');
            $this->ln();
        }
        
    }
	
	private function examenesRealizados(){
        $examenes= ( new Query())->select(['analisis.nombre','count(analisis.id) as cantidad','sum(analisis.precio) as valor'])
                                         ->from('orden')
                                         ->join('inner join','examen','orden_id =orden.id')
                                         ->join('inner join','analisis','analisis_id =analisis.id')                                         
                                         ->where(['between', 'fecha', $this->fecha_inicio, $this->fecha_fin])
                                         ->groupBy(['analisis.nombre'])
                                         ->orderBy(['cantidad'=>SORT_DESC])
                                         //->limit(10)
                                         ->all();
                                         //;
         $this->SetX($this->line_begin);
         $this->SetFont($this->font,'B',$this->font_body_size);
         $this->Cell(180,5,utf8_decode('ANALISIS REALIZADOS'),1,0,'C');
         $this->ln();
         
         $this->SetX($this->line_begin);
         $this->SetFont($this->font,'B',$this->font_body_size);
         $this->Cell(120,5,utf8_decode('ANALISIS'),1,0,'L');
         $this->Cell(30,5, 'CANTIDAD' ,1,0,'R');
		  $this->Cell(30,5, 'VENTA ($)' ,1,0,'R');
         $this->ln();
                                         
       foreach ($examenes as $examen) {
           $this->SetX($this->line_begin);
           $this->SetFont($this->font,'',$this->font_body_size);
           $this->Cell(120,5,utf8_decode( substr( $examen['nombre'],0 , 60) ),1,0,'L');
           $this->Cell(30,5, $examen['cantidad'] ,1,0,'R');
		    $this->Cell(30,5, $examen['valor'] ,1,0,'R');
           $this->ln();
        }
       
    }
	
	
	
    
    private  function graficoAnual(){        
        $query=  new Query();
        $query->select(['YEAR(Fecha) anio', 'MONTH(Fecha)  mes', 'SUM(precio) subtotal','IFNULL( SUM(descuento)  , 0 ) as descuento', 
            'SUM(valor_total) valor_total'])
        ->groupBy('anio,mes')
        ->from(['orden'])
        ->where(['YEAR(Fecha)'=> Date('Y',strtotime($this->fecha_inicio))]);
        $ventas=[];
        foreach ( $query->all() as $model) {
            $ventas[] = array( self::mes($model['mes']), $model['subtotal'], $model['valor_total'], $model['descuento'], );
           
        }
        
        
        $plot = new PHPlot(400, 200);
        $plot->SetImageBorderType('plain');
        
        $plot->SetPlotType('bars');
        $plot->SetDataType('text-data');
        $plot->SetDataValues($ventas);
        
        # Main plot title:
        $plot->SetTitle('Valores de Ordenes Realizadas');
        
        # Make a legend for the 3 data sets plotted:
        $plot->SetLegend(array('SubTotal', 'Valor Total', 'Descuento'));
        
        # Turn off X tick labels and ticks because they don't apply here:
        $plot->SetXTickLabelPos('none');
        $plot->SetXTickPos('none');
        # Make sure Y=0 is displayed:
        $plot->SetPlotAreaWorld(NULL, 0);
        //$plot->SetYDataLabelPos('plotin');
        //Disable image output
        $plot->SetPrintImage(false);
        $plot->DrawGraph();
        $this->GDImage($plot->img,15,120,180);
        
        $this->SetXY($this->line_begin,215);
        $this->SetFont($this->font,'B',$this->font_body_size);
        $this->Cell(60,5,utf8_decode('MESES'),1,0,'L');
        $this->Cell(40,5,'SUBTOTAL',1,0, 'R');
        $this->Cell(40,5,'DESCUENTO', 1,0,'R');
        $this->Cell(40,5,'VALOR TOTAL', 1,0,'R');        
        $this->ln();
        
        $this->SetFont($this->font,'',$this->font_body_size);
        foreach ( $ventas as $venta) {
            $this->SetX($this->line_begin);           
            $this->Cell(60,5,utf8_decode( $venta[0]),1,0,'L');
            $this->Cell(40,5,$venta[1],1,0,'R');            
            $this->Cell(40,5,$venta[3],1,0,'R');
            $this->Cell(40,5,$venta[2],1,0,'R');
            $this->ln();
        }    
    }
    
    public static function mes($id) {
        $meses=    [
            1=>'Enero',
            2=>'Febrero',
            3=>'Marzo',
            4=>'Abril',
            5=>'Mayo',
            6=>'Junio',
            7=>'Julio',
            8=>'Agosto',
            9=>'Septiembre',
            10=>'Octubre',
            11=>'Noviembre',
            12=>'Diciembre',
        ];
        
        return $meses[$id];
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
    
  