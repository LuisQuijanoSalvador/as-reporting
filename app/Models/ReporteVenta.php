<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReporteVenta extends Model
{
    use HasFactory;

    protected $table = 'vista_repventa';
    
    protected $fillable = [
        'Tipo', 'Documento', 'TipoDoc', 'NumeroBoleto', 'pasajero', 'Solicitante',
        'Ruta', 'TipoRuta', 'Counter', 'CentroCosto', 'Cod1', 'Cod2', 'Cod3', 'Cod4',
        'Cliente', 'Proveedor', 'FechaEmision', 'Moneda', 'TarifaNeta', 'Inafecto',
        'OtrosImpuestos', 'IGV', 'Total', 'idCliente'
    ];

    public $timestamps = false;

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'idCliente', 'id');
    }
}
