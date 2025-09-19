<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Empresa;
use App\Models\ReporteVenta;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            return $this->renderAdminDashboard()->title('Dashboard Administrador');
        } else {
            return $this->renderUserDashboard($user)->title('Dashboard de ' . $user->empresa->razonSocial);
        }
    }
    
    private function renderAdminDashboard()
    {
        $stats = [
            'usuarios' => User::count(),
            'empresas' => Empresa::count(),
            'ventas' => ReporteVenta::count(),
            'total_ventas' => ReporteVenta::sum('Total'),
        ];
        
        $ultimas_ventas = ReporteVenta::latest()->take(5)->get();
        
        return view('livewire.dashboard.admin', [
            'stats' => $stats,
            'ultimas_ventas' => $ultimas_ventas
        ])->layout('layouts.app');
    }
    
    private function renderUserDashboard($user)
    {
        $empresa = $user->empresa;
        
        $stats = [
            'ventas' => $empresa->ReporteVentas()->count(),
            'total_ventas' => $empresa->ReporteVentas()->sum('Total'),
        ];
        
        $ultimas_ventas = $empresa->ReporteVentas()->latest()->take(5)->get();
        
        return view('livewire.dashboard.user', [
            'stats' => $stats,
            'ultimas_ventas' => $ultimas_ventas,
            'empresa' => $empresa
        ])->layout('layouts.app');
    }
}