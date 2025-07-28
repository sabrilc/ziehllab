<?php
namespace app\modules\lab\pdfs;

use app\modules\lab\bussines\OrdenBussines;
use app\modules\site\models\Empresa;
use inquid\pdf\FPDF;
use utils\Texto;

class PDF_ORDEN_NO_PAGADA extends FPDF
{
    private $orden;
    private $empresa;

    public function __construct(OrdenBussines $orden)
    {
        $this->orden = $orden;
        $this->empresa = Empresa::findOne(1); // Ajusta según tu empresa

        parent::__construct('P', 'mm', 'A4');
        $this->AddPage();
        $this->SetMargins(15, 15, 15);
        $this->SetAutoPageBreak(true, 20);
    }

    public function construir()
    {
        // Logo empresa arriba (si existe)
        if ($this->empresa && file_exists(__DIR__ . '/../../../media/imagen/app/ziehllab_logo.png')) {
            $this->Image(__DIR__ . '/../../../media/imagen/app/ziehllab_logo.png', 15, 10, 70);
        }

        $this->SetY(40);
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, Texto::encodeLatin1('ORDEN DE ANÁLISIS NO PAGADA'), 0, 1, 'C');

        $this->Ln(10);

        // Datos básicos de la orden
        $this->SetFont('Arial', '', 12);
        $this->Cell(50, 10, Texto::encodeLatin1('Número de Orden:'), 0, 0);
        $this->Cell(0, 10, $this->orden->codigo, 0, 1);

        $this->Cell(50, 10, 'Paciente:', 0, 0);
        $this->Cell(0, 10, Texto::encodeLatin1($this->orden->paciente->nombres ?? '---'), 0, 1);

        $this->Cell(50, 10, 'Fecha:', 0, 0);
        $this->Cell(0, 10, date('d/m/Y', strtotime($this->orden->fecha)), 0, 1);

        $this->Ln(15);

        // Mensaje importante de no pago
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(255, 0, 0); // Rojo
        $this->MultiCell(0, 10, Texto::encodeLatin1("¡ATENCIÓN!\n\nLa orden no ha sido pagada.\nPor favor, realice el pago para continuar con el procesamiento y liberación de resultados."));

        $this->SetTextColor(0, 0, 0);

        $this->Ln(20);

        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, Texto::encodeLatin1('Gracias por su comprensión.'), 0, 1, 'C');
    }

    public function outputPDF()
    {
        $this->construir();
        return $this->Output('I', 'orden_no_pagada_' . $this->orden->id . '.pdf');
    }
}

