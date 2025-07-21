<?php
namespace app\modules\lab\pdfs;

use app\modules\lab\bussines\OrdenBussines;
use app\modules\site\models\Empresa;
use app\modules\lab\models\Orden;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Logo\Logo;
use inquid\pdf\FPDF;
use utils\Endroid\QrCode;
use utils\Endroid\QrCode\Writer\PngWriter;
use utils\Texto;
use Yii;

class PDF_ORDEN_TICKET extends FPDF
{
    private $orden;
    private $empresa;
    private $paciente;

    public function __construct(OrdenBussines $orden)
    {
        $this->orden = $orden;
        // Asumiendo que la empresa ID 1 es la que se usa para este tipo de recibo
        $this->empresa = Empresa::findOne(1);
        $this->paciente = $orden->paciente;

        parent::__construct('P', 'mm', 'A4');
        $this->AddPage();
        // Márgenes ajustados para parecerse al formato del informe
        $this->SetMargins(15, 15, 15);
        $this->SetAutoPageBreak(true, 20);
    }

    public function construir()
    {
        $this->Image(__DIR__ . '/../../../media/imagen/app/AccessLab_a4.png',0,0,210,297,'png');
        // --- Logo y Encabezado de la Empresa (Similar al informe) ---
        // El logo a la izquierda, información de la empresa a la derecha
        if ($this->empresa && file_exists(__DIR__ . '/../../../media/imagen/app/ziehllab_logo.png')) {
            $this->Image(__DIR__ . '/../../../media/imagen/app/ziehllab_logo.png', 15, 10, 70);
        }

        $this->SetY(10); // Posiciona el cursor para el texto del encabezado
        $this->SetX(50); // Mueve el cursor a la derecha para la información de la empresa

        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(218, 71, 22); // Color #da4716 para el nombre de la empresa
        $this->Cell(0, 8, strtoupper($this->empresa->razon_social ?? 'LABORATORIO'), 0, 1, 'R'); // Alinear a la derecha
        $this->SetTextColor(0, 0, 0); // Vuelve al color negro

        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 5, Texto::encodeLatin1(substr($this->empresa->direccion,0, 30) ??'BABAHOYO'), 0, 1, 'R'); // Ciudad "BABAHYO"
        // Asegúrate de que el email o la dirección de la empresa estén disponibles
        $this->Cell(0, 5, ($this->empresa->email ?? ''), 0, 1, 'R');
        $this->Ln(10); // Espacio después del encabezado de la empresa

        // --- Datos de la Orden (Número de Orden) ---
        // Similar a la sección "ORDEN NO. 2506136" del informe
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(0, 0, 0); // Asegura que el color sea negro para este texto
        $this->Cell(0, 10, 'ORDEN NO. ' . $this->orden->codigo, 0, 1, 'L');
        $this->Ln(2);

        // --- Datos del Paciente (Similar al informe) ---
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 7,  strtoupper( Texto::encodeLatin1($this->paciente->nombres) ?? 'PACIENTE DESCONOCIDO'), 0, 1, 'L');

        $this->SetFont('Arial', '', 10);
        $cedula = $this->paciente->cedula ?? '---';
        $edad = $this->paciente->getEdad() ?? '---'; // Asumiendo getEdad() en modelo Paciente
        $sexo = $this->paciente->sexo->descripcion ?? '---';

        // Formateado para parecerse al informe: Cédula: ..., Edad: ..., Sexo: ...
        $this->Cell(0, 6, Texto::encodeLatin1('Cédula: ' . $cedula . '  Edad: ' . $edad . ' años  Sexo: ' . $sexo), 0, 1, 'L');
        $this->Ln(5);

        // --- Fechas y Médico (Similar al informe) ---
        $this->SetFont('Arial', '', 9);
        $fechaIngreso = date('Y-m-d H:i', strtotime($this->orden->fecha)); // Usa la fecha de la orden como fecha de ingreso
        $fechaImpresion = date('Y-m-d H:i'); // Fecha y hora actual
        $medicoNombre = $this->orden->medico->nombre ?? '---'; // Asumiendo relación 'medico' en Orden

        // Alineación y espaciado para que se vea como en el informe
        $this->Cell(90, 5, 'Fecha de Ingreso: ' . $fechaIngreso, 0, 0, 'L');
        $this->Cell(0, 5, Texto::encodeLatin1('Fecha de Impresión: ' . $fechaImpresion), 0, 1, 'R');
        //$this->Cell(90, 5, 'Médico: ' . $medicoNombre, 0, 1, 'L'); // Deja esta línea solo para el médico
        $this->Ln(10); // Espacio antes de la sección de análisis

        // --- Título del Contenido "Informe de resultados" para mantener la estética ---
        // Aunque no son resultados, se usa el mismo encabezado para la similitud visual
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Informe de recibo', 0, 1, 'C'); // Cambiado a "Informe de recibo"
        $this->Ln(5);

        // --- Detalle de análisis (Ahora como en un recibo, no resultados) ---
        // Similar a tu código original de ticket para listar los exámenes y precios
        $this->SetFillColor(200, 200, 200); // Fondo gris para la cabecera de la tabla
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(20, 7, 'Cant', 1, 0, 'C', true);
        $this->Cell(90, 7, Texto::encodeLatin1('Análisis'), 1, 0, 'C', true);
        $this->Cell(30, 7, 'P.Unit', 1, 0, 'C', true);
        $this->Cell(30, 7, 'Total', 1, 1, 'C', true); // Última celda y nueva línea

        $this->SetFont('Arial', '', 10);
        $subtotal = 0;
        foreach ($this->orden->examenesParaImprimir as $examen) {
            $precio = $examen->precio;
            $subtotal += $precio;

            $this->Cell(20, 6, '1', 1, 0, 'C'); // Cantidad
            $this->Cell(90, 6, substr($examen->analisis->nombre, 0, 35), 1, 0, 'L'); // Nombre del análisis
            $this->Cell(30, 6, number_format($precio, 2), 1, 0, 'R'); // Precio Unitario
            $this->Cell(30, 6, number_format($precio, 2), 1, 1, 'R'); // Total por línea y nueva línea
        }

        $this->Ln(4);

        // --- Totales (Igual que en tu código original) ---
        $this->SetFont('Arial', '', 10);
        $this->Cell(140, 6, 'Subtotal', 0, 0, 'R');
        $this->Cell(30, 6, number_format($subtotal, 2), 0, 1, 'R');

        $descuento = $this->orden->descuento ?? 0;
        if ($descuento > 0) {
            $this->Cell(140, 6, 'Descuento', 0, 0, 'R');
            $this->Cell(30, 6, '- ' . number_format($descuento, 2), 0, 1, 'R');
        }

        $total = $this->orden->valor_total;
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(140, 8, 'TOTAL A PAGAR', 0, 0, 'R');
        $this->Cell(30, 8, number_format($total, 2), 0, 1, 'R');
        $this->Ln(5);

        // --- Agradecimiento ---
        $this->SetFont('Arial', 'I', 9);
        $this->Cell(0, 5, 'Gracias por confiar en nosotros.', 0, 1, 'C');
        $this->Ln(5);

        // --- Área de Validado por / Firma (Estética del informe) ---
        // No tiene sentido poner "Método: Microscopia" aquí, pero puedes poner "Recibido por:"
       // $this->SetFont('Arial', 'I', 9);
        //$this->Cell(0, 5, 'Emitido por: ' . ($this->orden->usuario->nombres ?? '---'), 0, 1, 'R'); // Asumiendo que la orden tiene un usuario asociado
       // $this->Ln(10);

        // Espacio para firma o sello
        //$this->Cell(0, 5, '__________________________', 0, 1, 'R');
        //$this->Cell(0, 5, 'Firma / Sello', 0, 1, 'R'); // Texto genérico para el pie

        // --- QR (Posicionado abajo a la izquierda como en el informe) ---
        $this->SetY($this->GetPageHeight() - 70); // Ajusta la posición Y para el QR
        $this->SetX(15); // Posiciona en el margen izquierdo
        $this->QrCode(); // Llama a tu método QrCode
    }

    public function QrCode()
    {
        // 1. Crear el contenido del QR
        $data = (strlen($this->orden->token) > 50)
            ? 'https://' . $_SERVER['HTTP_HOST'] . '/documentos/orden?token=' . $this->orden->token
            : $this->orden->codigo;

        // 2. Crear el QR con colores y tamaño
        $qrCode = QrCode::create($data)
            //->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->setSize(300)
            ->setMargin(10)
            ->setForegroundColor(new Color(0, 0, 0)) // Azul
            ->setBackgroundColor(new Color(255, 255, 255)); // Blanco

        // 3. Agregar logo (debe existir el archivo)
        $logo = Logo::create(__DIR__ . '/../../../media/imagen/app/ziehllab_logo.png') // Asegúrate que esta ruta esté correcta
        ->setResizeToWidth(150)
            ->setResizeToHeight(60);

        // 5. Generar el QR con logo y texto usando PngWriter
        $writer = new PngWriter();
        $result = $writer->write($qrCode, $logo);

        // 6. Convertir el resultado a base64 (data URI)
        $dataUri = $result->getDataUri();

        // 7. Insertar imagen QR en el PDF
        $this->MultiCell(50, 5, 'Escanea el QR para consultar resultados ', 0, 'L', 0);
        $this->Image($dataUri, 15, $this->GetY(), 40, 40, 'png');
    }

/*
    public function QrCode()
    {
        // Tu método QrCode genera un solo QR, que es adecuado para un recibo.
        $data = (strlen($this->orden->token) > 50)
            ? 'https://' . $_SERVER['HTTP_HOST'] . '/documentos/orden?token=' . $this->orden->token // Cambiado a 'orden' si el token es para verificar la orden
            : $this->orden->codigo;

        /*$qrCode = QrCode::create($data)
           // ->setEncoding(new Encoding('UTF-8'))
            ->setSize(200)
            ->setMargin(10);

        $qrCode = QrCode::create($data)
            ->setSize(300)
            ->setMargin(10)
            ->setForegroundColor(new Color(33, 97, 255)) // Color azul
            ->setBackgroundColor(new Color(255, 255, 255)); // Blanco;

        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $dataUri = $result->getDataUri();
        // 5. Insertar la imagen en FPDF usando el Data URI
        $this->MultiCell(50, 5, 'Escanea el QR para consultar resultados ', 0, 'L', 0);
        $this->Image($dataUri, 15, $this->GetY(), 40, 40, 'png');

    }*/

    public function outputPDF()
    {
        $this->construir();
        return $this->Output('I', 'recibo_orden_' . $this->orden->id . '.pdf'); // Cambiado el nombre del archivo
    }

    public function printBase64()
    {
        $this->construir();
        return base64_encode($this->Output('', 'S'));
    }
}