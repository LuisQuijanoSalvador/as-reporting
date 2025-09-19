<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class RepVentasExport implements FromCollection, WithHeadings, ShouldAutoSize, WithTitle, WithStyles, WithMapping
{
    protected $fechaInicio;
    protected $fechaFin;
    protected $search;
    protected $userRole;
    protected $idCliente;
    protected $selectedEmpresaId;
    protected $reporteTitulo;

    public function __construct($fechaInicio, $fechaFin, $search, $userRole, $idCliente, $selectedEmpresaId, $reporteTitulo)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->search = $search;
        $this->userRole = $userRole;
        $this->idCliente = $idCliente;
        $this->selectedEmpresaId = $selectedEmpresaId;
        $this->reporteTitulo = $reporteTitulo;
    }

    public function collection()
    {
        $query = DB::table('vista_repventa');

        // Filtros
        if ($this->userRole === 'admin') {
            if ($this->selectedEmpresaId) {
                $query->where('idCliente', $this->selectedEmpresaId);
            }
        } else {
            $query->where('idCliente', $this->idCliente);
        }

        if ($this->fechaInicio && $this->fechaFin) {
            $query->whereBetween('FechaEmision', [$this->fechaInicio, $this->fechaFin]);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('pasajero', 'like', '%' . $this->search . '%')
                  ->orWhere('Documento', 'like', '%' . $this->search . '%')
                  ->orWhere('NumeroBoleto', 'like', '%' . $this->search . '%')
                  ->orWhere('Solicitante', 'like', '%' . $this->search . '%')
                  ->orWhere('Ruta', 'like', '%' . $this->search . '%');
            });
        }

        return $query->get();
    }

    // Mapeo para formatear los datos (ej. decimales)
    public function map($row): array
    {
        return [
            $row->Tipo,
            $row->FechaEmision,
            $row->NumeroBoleto,
            $row->Documento,
            $row->pasajero,
            $row->Solicitante,
            $row->Ruta,
            $row->TipoRuta,
            $row->Counter,
            $row->CentroCosto,
            $row->Cod1,
            $row->Cod2,
            $row->Cod3,
            $row->Cod4,
            $row->Moneda,
            number_format($row->TarifaNeta, 2),
            number_format($row->Inafecto, 2),
            number_format($row->OtrosImpuestos, 2),
            number_format($row->IGV, 2),
            number_format($row->Total, 2),
        ];
    }

    public function headings(): array
    {
        return [
            'Tipo',
            'Fecha Emisión',
            'Número Boleto',
            'Documento',
            'Pasajero',
            'Solicitante',
            'Ruta',
            'Tipo Ruta',
            'Counter',
            'Centro Costo',
            'Cod1',
            'Cod2',
            'Cod3',
            'Cod4',
            'Moneda',
            'Tarifa Neta',
            'Inafecto',
            'Otros Impuestos',
            'IGV',
            'Total',
        ];
    }

    public function title(): string
    {
        return 'Reporte de Compras';
    }

    public function styles(Worksheet $sheet)
    {
        // Título del reporte y rango de fechas
        $sheet->mergeCells('A1:B1');
        $sheet->setCellValue('A1', $this->reporteTitulo);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

        $sheet->mergeCells('A2:B2');
        $sheet->setCellValue('A2', 'Rango de Fechas: ' . Carbon::parse($this->fechaInicio)->format('d/m/Y') . ' - ' . Carbon::parse($this->fechaFin)->format('d/m/Y'));
        $sheet->getStyle('A2')->getFont()->setSize(12);

        // Estilos para los encabezados
        $sheet->getStyle('A4:T4')->applyFromArray([
            'font' => ['bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E0E0E0']]
        ]);

        // Mueve los datos a partir de la fila 4
        $sheet->fromArray($this->headings(), null, 'A4');
        $sheet->fromArray($this->map($this->collection()), null, 'A5');
    }
}