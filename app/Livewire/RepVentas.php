<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RepVentasExport;
use App\Exports\VentasExport;
use App\Exports\VentasTritonExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Empresa;
use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Models\ClientFieldConfiguration;

class RepVentas extends Component
{
    use WithPagination;

    // Propiedades para los filtros
    public $fechaInicio;
    public $fechaFin;
    public $search = '';
    public $selectedEmpresaId = null;
    public $empresas;
    public $userRole;
    public $idCliente = null;
    public $clientConfig;

    // Título del reporte para la exportación
    public $reporteTitulo = 'REPORTE DE COMPRAS';

    // Escucha los cambios en las propiedades de búsqueda
    protected $queryString = [
        'fechaInicio' => ['except' => ''],
        'fechaFin' => ['except' => ''],
        'search' => ['except' => ''],
        'selectedEmpresaId' => ['except' => null],
    ];

    // Montar el componente al inicio
    public function mount()
    {
        $this->userRole = Auth::user()->role;
        if ($this->userRole === 'user') {
            $this->idCliente = Auth::user()->empresa_id;
        }
        // Si el usuario es admin, carga la lista de empresas
        if ($this->userRole === 'admin') {
            $this->empresas = Empresa::all();
        }

        // Establecer fechas por defecto (por ejemplo, el último mes)
        if (empty($this->fechaFin)) {
            $this->fechaFin = now()->format('Y-m-d');
        }

        if (empty($this->fechaInicio)) {
            $this->fechaInicio = now()->subMonth()->format('Y-m-d');
        }

        $this->loadClientConfiguration();
    }

    public function loadClientConfiguration()
    {
        // Determinar qué ID de cliente usar
        $clientIdToUse = $this->idCliente;

        if ($this->userRole === 'admin' && $this->selectedEmpresaId) {
            $clientIdToUse = $this->selectedEmpresaId;
        }

        if ($clientIdToUse) {
            // Cargar la configuración. Usamos firstOrNew por si el cliente no tiene una configuración guardada
            $this->clientConfig = ClientFieldConfiguration::firstOrNew(
                ['client_id' => $clientIdToUse], 
                [ // Valores por defecto si es nuevo
                    'cod1_is_visible' => false, 'cod1_label' => 'Cod1',
                    'cod2_is_visible' => false, 'cod2_label' => 'Cod2',
                    'cod3_is_visible' => false, 'cod3_label' => 'Cod3',
                    'cod4_is_visible' => false, 'cod4_label' => 'Cod4',
                ]
            );
        } else {
            // Si no hay cliente (ej: admin sin seleccionar), inicializamos con valores predeterminados
            $this->clientConfig = new ClientFieldConfiguration();
        }
    }

    // Resetea la página de paginación al cambiar los filtros
    public function updatingFechaInicio()
    {
        $this->resetPage();
    }

    public function updatingFechaFin()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedEmpresaId()
    {
        $this->resetPage();
        $this->loadClientConfiguration(); // Recargar la configuración
    }

    // Lógica de renderizado
    public function render()
    {
        $query = DB::table('vista_repventa');

        // Filtro por rol y empresa
        if ($this->userRole === 'admin') {
            if ($this->selectedEmpresaId) {
                $query->where('idCliente', $this->selectedEmpresaId);
            }
        } else {
            $query->where('idCliente', $this->idCliente);
        }
        
        // Filtro de fecha
        if ($this->fechaInicio && $this->fechaFin) {
            $query->whereBetween('FechaEmision', [$this->fechaInicio, $this->fechaFin]);
        }

        // Filtro de búsqueda general
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('pasajero', 'like', '%' . $this->search . '%')
                  ->orWhere('Documento', 'like', '%' . $this->search . '%')
                  ->orWhere('NumeroBoleto', 'like', '%' . $this->search . '%')
                  ->orWhere('Solicitante', 'like', '%' . $this->search . '%')
                  ->orWhere('Ruta', 'like', '%' . $this->search . '%');
            });
        }

        $datos = $query->paginate(3); // Paginación de 15 registros por página
        $this->loadClientConfiguration();
        return view('livewire.rep-ventas', [
            'datos' => $datos,
            'clientConfig' => $this->clientConfig,
        ]);
    }

    // --- Lógica de exportación ---

    public function exportarExcel(Request $request)
    {
        $user = auth()->user();
        $empresa_id = $request->input('empresa');
        $fecha_inicial = $request->input('fecha_inicial');
        $fecha_final = $request->input('fecha_final');
        
        $clientIdToUse = $this->idCliente ?? $this->selectedEmpresaId;

        $clientConfig = ClientFieldConfiguration::firstOrNew(
            ['client_id' => $clientIdToUse],
            [ // Asegurar valores por defecto si no existe
                'cod1_is_visible' => false, 'cod1_label' => 'Cod1',
                'cod2_is_visible' => false, 'cod2_label' => 'Cod2',
                'cod3_is_visible' => false, 'cod3_label' => 'Cod3',
                'cod4_is_visible' => false, 'cod4_label' => 'Cod4',
            ]
        );

        if($clientIdToUse){
            // if($this->idCliente == 1036){
            //     return Excel::download(new VentasTritonExport($user, $clientConfig, $this->selectedEmpresaId,$this->idCliente, $this->fechaInicio, $this->fechaFin), 'reporte_compras_' . date('Y-m-d') . '.xlsx');
            // } else{
                return Excel::download(new VentasExport($user, $clientConfig, $this->selectedEmpresaId,$this->idCliente, $this->fechaInicio, $this->fechaFin), 'reporte_compras_' . date('Y-m-d') . '.xlsx');
            // }
        // }else{
        //     if($this->selectedEmpresaId){
        //         if($this->selectedEmpresaId == 1036){
        //             return Excel::download(new VentasTritonExport($user, $this->selectedEmpresaId,$this->idCliente, $this->fechaInicio, $this->fechaFin), 'reporte_compras_' . date('Y-m-d') . '.xlsx');
        //         } else{
        //             return Excel::download(new VentasExport($user, $this->selectedEmpresaId,$this->idCliente, $this->fechaInicio, $this->fechaFin), 'reporte_compras_' . date('Y-m-d') . '.xlsx');
        //         }
        //     }
        
        }
    }

    public function exportarPDF()
    {
        $cliente = null;
        if ($this->userRole === 'admin') {
            if ($this->selectedEmpresaId) {
                // $query->where('idCliente', $this->selectedEmpresaId);
                $cliente = Cliente::where('idcliente', $this->selectedEmpresaId)->first();
            }
        } else {
            // $query->where('idCliente', $this->idCliente);
            $cliente = Cliente::where('idcliente', $this->idCliente)->first();
        }

        $empresa = Empresa::find($this->idCliente);
        
        // dd($cliente);
        // $empresa = null;
        // if ($this->userRole === 'user' || ($this->userRole === 'admin' && $this->selectedEmpresaId)) {
        //     $id = ($this->userRole === 'user') ? $this->idCliente : $this->selectedEmpresaId;
        //     $empresa = Empresa::find($id);
        // }

        // Si la empresa tiene logos, obtén la ruta absoluta
        $logoEmpresaPath = null;
        $logoClientePath = null;
        $logoEmpresaPath = public_path('storage/logo-as-travel.png');
        if ($cliente) {
            
            // if ($empresa->logoEmpresa) {
            //     // $logoEmpresaPath = storage_path('app/public/' . $empresa->logoEmpresa);
            //     $logoEmpresaPath = public_path('storage/logo-as-travel.png');
            // }
            if ($cliente->logo) {
                $logoClientePath = public_path('storage/' . $cliente->logo);
            }
        }

        // Obtener los logos como datos Base64
        // $logoEmpresaBase64 = null;
        // $logoPath = storage_path('app/public/logo-as-travel.png');
        // if (file_exists($logoPath)) {
        //     $logoEmpresaBase64 = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($logoPath));
        // }
        // dd($logoPath);
        // $logoClienteBase64 = null;
        

        // Lógica para exportar a PDF
        $query = DB::table('vista_repventa');

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

        $datos = $query->get();

        $empresa = null;
        if ($this->userRole === 'user' || ($this->userRole === 'admin' && $this->selectedEmpresaId)) {
            $id = ($this->userRole === 'user') ? $this->idCliente : $this->selectedEmpresaId;
            $empresa = Empresa::find($id);
        }

        $pdf = PDF::loadView('livewire.pdf.reporte-ventas-pdf', [
            'datos' => $datos,
            'fechaInicio' => $this->fechaInicio,
            'fechaFin' => $this->fechaFin,
            'reporteTitulo' => $this->reporteTitulo,
            'empresa' => $empresa,
            'logoEmpresa' => $logoEmpresaPath,
            'logoCliente' => $logoClientePath,
            'clientConfig' => $this->clientConfig,
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'reporte_compras.pdf');
    }
}