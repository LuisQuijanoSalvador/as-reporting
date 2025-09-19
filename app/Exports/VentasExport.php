<?php

namespace App\Exports;

use App\Models\ReporteVenta;
use App\Models\User;
use App\Models\Cliente;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class VentasExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithDrawings
{
    protected $user;
    protected $empresa_id;
    protected $fecha_inicial;
    protected $fecha_final;
    public $logoClientePath = null;

    public function __construct(User $user, $empresa_id = null, $fecha_inicial = null, $fecha_final = null)
    {
        $this->user = $user;
        $this->empresa_id = $empresa_id;
        $this->fecha_inicial = $fecha_inicial;
        $this->fecha_final = $fecha_final;
    }

    public function collection()
    {
        $query = ReporteVenta::query();
        
        if ($this->user->role === 'user') {
            $query->where('idCliente', $this->user->empresa_id);
        } else {
            if ($this->empresa_id) {
                $query->where('idCliente', $this->empresa_id);
            }
        }
        
        if ($this->fecha_inicial && $this->fecha_final) {
            $query->whereBetween('FechaEmision', [$this->fecha_inicial, $this->fecha_final]);
        }
        
        return $query->orderBy('FechaEmision', 'desc')->get()->map(function ($venta) {
            return [
                'Tipo' => $venta->Tipo,
                'FechaEmision' => $venta->FechaEmision,
                'NumeroBoleto' => $venta->NumeroBoleto,
                'Documento' => $venta->Documento,
                'pasajero' => $venta->pasajero,
                'Solicitante' => $venta->Solicitante,
                'Ruta' => $venta->Ruta,
                'TipoRuta' => $venta->TipoRuta,
                // 'Counter' => $venta->Counter,
                'CentroCosto' => $venta->CentroCosto,
                'Cod1' => $venta->Cod1,
                'Cod2' => $venta->Cod2,
                'Cod3' => $venta->Cod3,
                'Cod4' => $venta->Cod4,
                // 'Cliente' => $venta->Cliente,
                'Moneda' => $venta->Moneda,
                'TarifaNeta' => $venta->TarifaNeta,
                'Inafecto' => $venta->Inafecto,
                'OtrosImpuestos' => $venta->OtrosImpuestos,
                'IGV' => $venta->IGV,
                'Total' => $venta->Total,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Tipo',
            'FechaEmision',
            'NumeroBoleto',
            'Documento',
            'Pasajero',
            'Solicitante',
            'Ruta',
            'TipoRuta',
            // 'Counter',
            'CentroCosto',
            'Cod1',
            'Cod2',
            'Cod3',
            'Cod4',
            // 'Cliente',
            'Moneda',
            'TarifaNeta',
            'Inafecto',
            'OtrosImpuestos',
            'IGV',
            'Total'
        ];
    }

    public function registerEvents(): array
    {
        $cliente = null;
        if ($this->user->role === 'user') {
            // $query->where('idCliente', $this->user->empresa_id);
            $cliente = Cliente::where('idcliente', $this->user->empresa_id)->first();
        } else {
            if ($this->empresa_id) {
                // $query->where('idCliente', $this->empresa_id);
                $cliente = Cliente::where('idcliente', $this->empresa_id)->first();
            }
        }
        // dd($this->empresa_id);
        // $cliente = Cliente::where('idcliente', $this->empresa_id)->first();
        if ($cliente) {
            if ($cliente->logo) {
                $this->logoClientePath = public_path('storage/' . $cliente->logo);
            }
        }else{
            $this->logoClientePath = public_path('img/logo-as-travel.png');
        }

        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Configurar orientación horizontal
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                
                // Configurar márgenes
                $sheet->getPageMargins()->setLeft(0.5);
                $sheet->getPageMargins()->setRight(0.5);
                $sheet->getPageMargins()->setTop(0.5);
                $sheet->getPageMargins()->setBottom(0.5);
                
                // Insertar logo de AS Travel (izquierda)
                $drawing = new Drawing();
                $drawing->setName('AS Travel Logo');
                $drawing->setDescription('Logo de AS Travel');
                $drawing->setPath(public_path('img/logo-as-travel.png'));
                $drawing->setHeight(50);
                $drawing->setCoordinates('A1');
                $drawing->setWorksheet($sheet);
                
                // Insertar logo del cliente (derecha)
                $drawing2 = new Drawing();
                $drawing2->setName('Cliente Logo');
                $drawing2->setDescription('Logo del Cliente');
                $drawing2->setPath($this->logoClientePath);
                $drawing2->setHeight(50);
                $drawing2->setCoordinates('Q1');
                $drawing2->setWorksheet($sheet);
                
                // Agregar título centrado
                // $sheet->setCellValue('G1', 'REPORTE DE COMPRAS');
                // $sheet->setCellValue('G2', date('d/m/Y'));
                // $sheet->getStyle('G1:G2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                // $sheet->getStyle('G1')->getFont()->setBold(true);
                // $sheet->getStyle('G1')->getFont()->setSize(16);
                
                // Insertar 4 filas vacías al principio
                $sheet->insertNewRowBefore(1, 4);
                
                // Mover los logos y título a las nuevas posiciones
                $drawing->setCoordinates('A1');
                $drawing2->setCoordinates('Q1');
                $sheet->setCellValue('G1', 'REPORTE DE COMPRAS');
                // $sheet->setCellValue('G2', date('d/m/Y'));
                $sheet->setCellValue('G2', 'Del ' . Carbon::parse($this->fecha_inicial)->format('d/m/Y') . ' al ' . Carbon::parse($this->fecha_final)->format('d/m/Y'));
                $sheet->getStyle('G1:G2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G1')->getFont()->setBold(true);
                $sheet->getStyle('G1')->getFont()->setSize(16);
                
                // Establecer encabezados en la fila 5
                $headersRange = 'A5:S5';
                $sheet->getStyle($headersRange)->getFont()->setBold(true);
                $sheet->getStyle($headersRange)->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle($headersRange)->getFill()->getStartColor()->setARGB('592a56');
                $sheet->getStyle($headersRange)->getFont()->getColor()->setARGB('FFFFFF');
                
                // Centrar encabezados
                $sheet->getStyle($headersRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Aplicar bordes a la tabla
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                $tableRange = 'A5:' . $highestColumn . $highestRow;
                
                $sheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                
                // Formato de columna de totales
                $highestRow = $sheet->getHighestRow();
                $totalRow = $highestRow;
                $sheet->getStyle('A' . $totalRow . ':W' . $totalRow)->getFont()->setBold(true);
                $sheet->getStyle('A' . $totalRow . ':W' . $totalRow)->getFill()->setFillType(Fill::FILL_SOLID);
                $sheet->getStyle('A' . $totalRow . ':W' . $totalRow)->getFill()->getStartColor()->setARGB('e9ecef');
                
                // Alinear columnas numéricas a la derecha
                $numericColumns = ['U', 'V', 'W', 'X', 'Y', 'Z'];
                foreach ($numericColumns as $col) {
                    $sheet->getStyle($col . '6:' . $col . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }
                
                // Limpiar las primeras 4 filas (quitar bordes si los hay)
                for ($row = 1; $row <= 4; $row++) {
                    $sheet->getStyle('A' . $row . ':W' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_NONE);
                }
            },
        ];
    }
    
    public function drawings()
    {
        return [];
    }
}