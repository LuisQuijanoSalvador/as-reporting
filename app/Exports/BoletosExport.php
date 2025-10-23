<?php

namespace App\Exports;

use App\Models\ReporteBoleto;
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
use App\Models\ClientFieldConfiguration;

class BoletosExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithDrawings
{
    protected $user;
    protected $empresa_id;
    protected $fecha_inicial;
    protected $fecha_final;
    public $logoClientePath = null;
    public $clienteId;
    protected $clientConfig;

    public function __construct(User $user, ClientFieldConfiguration $clientConfig, $empresa_id = null, $cliente_id = null, $fecha_inicial = null, $fecha_final = null)
    {
        $this->user = $user;
        $this->empresa_id = $empresa_id;
        $this->clienteId = $cliente_id;
        $this->fecha_inicial = $fecha_inicial;
        $this->fecha_final = $fecha_final;
        $this->clientConfig = $clientConfig;
    }

    public function collection()
    {
        $query = ReporteBoleto::query();
        
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
            $fila = [
                'fechaEmision' => Carbon::parse($venta->fechaEmision)->startOfDay()->format('d/m/Y'),
                'Boleto' => $venta->Boleto,
                'Documento' => $venta->Documento,
                'Pasajero' => $venta->Pasajero,
                'Solicitante' => $venta->Solicitante,
                'centroCosto' => $venta->centroCosto,
            ];
            // Añadir Cod1 si está visible
            if ($this->clientConfig->cod1_is_visible) {
                $fila[] = $venta->Cod1;
            }
            // Añadir Cod2 si está visible
            if ($this->clientConfig->cod2_is_visible) {
                $fila[] = $venta->Cod2;
            }
            // Añadir Cod3 si está visible
            if ($this->clientConfig->cod3_is_visible) {
                $fila[] = $venta->Cod3;
            }
            // Añadir Cod4 si está visible
            if ($this->clientConfig->cod4_is_visible) {
                $fila[] = $venta->Cod4;
            } 
            $fila['Aerolinea'] = $venta->Aerolinea;
            $fila['Clase'] = $venta->Clase;
            $fila['fechaSalida'] = $venta->fechaSalida;
            $fila['fechaLlegada'] = $venta->fechaLlegada;
            $fila['Ruta'] = $venta->Ruta;
            $fila['Moneda'] = $venta->Moneda;
            $fila['tarifaNeta'] = $venta->tarifaNeta;
            $fila['Inafecto'] = $venta->Inafecto;
            $fila['IGV'] = $venta->igv;
            $fila['otrosImpuestos'] = $venta->otrosImpuestos;
            $fila['Total'] = $venta->total;
            $fila['NetoFee'] = $venta->NetoFee;
            $fila['IGVFee'] = $venta->IGVFee;
            $fila['TotalFee'] = $venta->TotalFee;
        
            return $fila;
        
        });
    }

    public function headings(): array
    {
        $base = [
            'Fecha',
            'Boleto',
            'Documento',
            'Pasajero',
            'Solicitante',
            'CentroCosto',
        ];
    
        $codColumns = [];
    
        // Si Cod1 es visible según la configuración, lo añadimos con su etiqueta
        if ($this->clientConfig->cod1_is_visible) {
            $codColumns[] = $this->clientConfig->cod1_label ?? 'Cod1';
        }
        // Si Cod2 es visible según la configuración, lo añadimos con su etiqueta
        if ($this->clientConfig->cod2_is_visible) {
            $codColumns[] = $this->clientConfig->cod2_label ?? 'Cod2';
        }
        // Si Cod3 es visible según la configuración, lo añadimos con su etiqueta
        if ($this->clientConfig->cod3_is_visible) {
            $codColumns[] = $this->clientConfig->cod3_label ?? 'Cod3';
        }
        // Si Cod4 es visible según la configuración, lo añadimos con su etiqueta
        if ($this->clientConfig->cod4_is_visible) {
            $codColumns[] = $this->clientConfig->cod4_label ?? 'Cod4';
        }
        
        // Fusionamos las columnas base con las columnas CodX
        $base = array_merge($base, $codColumns);
    
        $base = array_merge($base, [
            'Aerolinea',
            'Clase',
            'FechaSalida',
            'FechaLlegada',
            'Ruta',
            'Moneda',
            'Neto',
            'Inafecto',
            'IGV',
            'OtrosImpuestos',
            'Total',
            'NetoFee',
            'IGVFee',
            'TotalFee',
        ]);
    
        return $base;
    
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
                $drawing2->setCoordinates('V1');
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
                $drawing2->setCoordinates('V1');
                $sheet->setCellValue('G1', 'REPORTE DE BOLETOS');
                // $sheet->setCellValue('G2', date('d/m/Y'));
                $sheet->setCellValue('G2', 'Del ' . Carbon::parse($this->fecha_inicial)->format('d/m/Y') . ' al ' . Carbon::parse($this->fecha_final)->format('d/m/Y'));
                $sheet->getStyle('G1:G2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G1')->getFont()->setBold(true);
                $sheet->getStyle('G1')->getFont()->setSize(16);
                
                // Establecer encabezados en la fila 5
                $headersRange = 'A5:X5';
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

                //
                $totalRow = $highestRow + 1;

                $sheet->setCellValue("P{$totalRow}", "Totales");
                $sheet->getStyle("P{$totalRow}")->getFont()->setBold(true);
                $sheet->getStyle("P{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Insertar fórmula de totales en cada columna
                $sheet->setCellValue("Q{$totalRow}", "=SUM(Q6:Q{$highestRow})");
                $sheet->setCellValue("R{$totalRow}", "=SUM(R6:R{$highestRow})");
                $sheet->setCellValue("S{$totalRow}", "=SUM(S6:S{$highestRow})");
                $sheet->setCellValue("T{$totalRow}", "=SUM(T6:T{$highestRow})");
                $sheet->setCellValue("U{$totalRow}", "=SUM(U6:U{$highestRow})");
                $sheet->setCellValue("V{$totalRow}", "=SUM(V6:V{$highestRow})");
                $sheet->setCellValue("W{$totalRow}", "=SUM(W6:W{$highestRow})");
                $sheet->setCellValue("X{$totalRow}", "=SUM(X6:X{$highestRow})");

                // Aplicar formato de 2 decimales
                foreach (['Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X'] as $col) {
                    $sheet->getStyle("{$col}6:{$col}{$totalRow}")
                        ->getNumberFormat()
                        ->setFormatCode('#,##0.00');

                    // Alinear a la derecha también la fila de totales
                    $sheet->getStyle("{$col}{$totalRow}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    // Estilo visual para la fila de totales
                    $sheet->getStyle("{$col}{$totalRow}")->getFont()->setBold(true);
                    $sheet->getStyle("{$col}{$totalRow}")->getFill()->setFillType(Fill::FILL_SOLID);
                    $sheet->getStyle("{$col}{$totalRow}")->getFill()->getStartColor()->setARGB('e9ecef');
                }
                
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
