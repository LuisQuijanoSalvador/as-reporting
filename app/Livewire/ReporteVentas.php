<?php

namespace App\Livewire;

use App\Models\Empresa;
use App\Models\ReporteVenta;
use Livewire\Component;
use Livewire\WithPagination;

class ReporteVentas extends Component
{
    use WithPagination;
    
    public $empresa_id = '';
    public $fecha_inicial = '';
    public $fecha_final = '';
    public $empresas;
    public $perPage = 10; 
    
    public function mount()
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            $this->empresas = Empresa::all();
        } else {
            $this->empresa_id = $user->empresa_id;
        }
        
        // Establecer fechas por defecto (últimos 30 días)
        $this->fecha_inicial = date('Y-m-d', strtotime('-30 days'));
        $this->fecha_final = date('Y-m-d');
    }
    
    public function render()
    {
        $query = ReporteVenta::query();
        
        // Filtrar por empresa si está seleccionada
        if ($this->empresa_id) {
            $query->where('idCliente', $this->empresa_id);
        }
        
        // Filtrar por rango de fechas
        if ($this->fecha_inicial && $this->fecha_final) {
            $query->whereBetween('FechaEmision', [$this->fecha_inicial, $this->fecha_final]);
        }
        
        $ventas = $query->orderBy('FechaEmision', 'desc')->paginate($this->perPage);
        
        return view('livewire.reporte-ventas', [
            'ventas' => $ventas,
            'empresas' => $this->empresas
        ])->layout('layouts.app');
    }
    
    public function filtrar()
    {
        // Resetear la paginación al filtrar
        $this->resetPage();
    }
    
    public function limpiarFiltros()
    {
        $this->reset(['empresa_id', 'fecha_inicial', 'fecha_final']);
        
        // Restablecer fechas por defecto
        $this->fecha_inicial = date('Y-m-d', strtotime('-30 days'));
        $this->fecha_final = date('Y-m-d');
        
        $this->resetPage();
    }
    
    public function exportExcel()
    {
        return redirect()->route('reportes.ventas.excel', [
            'empresa_id' => $this->empresa_id,
            'fecha_inicial' => $this->fecha_inicial,
            'fecha_final' => $this->fecha_final
        ]);
    }
    
    public function exportPDF()
    {
        return redirect()->route('reportes.ventas.pdf', [
            'empresa_id' => $this->empresa_id,
            'fecha_inicial' => $this->fecha_inicial,
            'fecha_final' => $this->fecha_final
        ]);
    }
}