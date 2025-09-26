<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ReporteController;
use App\Livewire\Dashboard;
use App\Livewire\UsuarioIndex;
use App\Livewire\ReporteVentas;
use App\Livewire\RepVentas;
use App\Livewire\ReporteBoletos;
use App\Livewire\LogosClientes;

// Ruta raíz
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de autenticación
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    // Dashboard principal
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    
    // Rutas para administradores
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/usuarios', UsuarioIndex::class)->name('usuarios.index');
        Route::get('/gestion-logos', LogosClientes::class)->name('logos.gestion');
    });
    
    // Rutas para reportes
    Route::get('/reportes/ventas', ReporteVentas::class)->name('reportes.ventas');
    Route::get('/reportes/ventas/excel', [ReporteController::class, 'exportExcel'])->name('reportes.ventas.excel');
    Route::get('/reportes/ventas/pdf', [ReporteController::class, 'exportPDF'])->name('reportes.ventas.pdf');

    Route::get('/reporte-ventas', RepVentas::class)->middleware('auth')->name('repventas');
    Route::get('/reporte-boletos', ReporteBoletos::class)->middleware('auth')->name('repboletos');
});