<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReporteVenta;
use App\Models\Empresa;
use App\Exports\VentasExport;
use Maatwebsite\Excel\Facades\Excel;
// use PDF;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    public function exportExcel(Request $request)
    {
        $user = auth()->user();
        $empresa_id = $request->input('empresa_id');
        $fecha_inicial = $request->input('fecha_inicial');
        $fecha_final = $request->input('fecha_final');
        
        return Excel::download(new VentasExport($user, $empresa_id, $fecha_inicial, $fecha_final), 'reporte_compras_' . date('Y-m-d') . '.xlsx');
    }
    
    public function exportPDF(Request $request)
    {
        $user = auth()->user();
        $empresa_id = $request->input('empresa_id');
        $fecha_inicial = $request->input('fecha_inicial');
        $fecha_final = $request->input('fecha_final');
        
        $query = ReporteVenta::query();

        $logoEmpresaPath = public_path('storage/logo-as-travel.png');
        
        if ($user->role === 'user') {
            $query->where('idCliente', $user->empresa_id);
        } else {
            if ($empresa_id) {
                $query->where('idCliente', $empresa_id);
            }
        }
        
        if ($fecha_inicial && $fecha_final) {
            $query->whereBetween('FechaEmision', [$fecha_inicial, $fecha_final]);
        }
        
        $ventas = $query->orderBy('FechaEmision', 'desc')->get();
        
        // Configurar PDF con opciones necesarias para imágenes
        // $pdf = PDF::loadView('reportes.ventas_pdf', compact('ventas'));
        $pdf = PDF::loadView('reportes.ventas_pdf', [
            'ventas' => $ventas,
            // 'fechaInicio' => $this->fechaInicio,
            // 'fechaFin' => $this->fechaFin,
            // 'reporteTitulo' => $this->reporteTitulo,
            // 'empresa' => $empresa,
            'logoEmpresa' => $logoEmpresaPath,
            // 'logoClienteBase64' => $logoClienteBase64,
        ]);

        $pdf->setPaper('A4', 'landscape');
        $pdf->setOptions([
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false, // Importante para cargar imágenes externas
            'isFontSubsettingEnabled' => true,
            'defaultFont' => 'Arial',
            'dpi' => 150,
        ]);
        $pdf->render();
        // return $pdf->download('reporte_compras_' . date('Y-m-d') . '.pdf');
        return response()->streamDownload(function() use($pdf){
            echo $pdf->stream();
        },'reporte_compras_' . date('Y-m-d') . '.pdf');
    }
}