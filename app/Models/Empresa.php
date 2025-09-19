<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'vista_empresas';
    
    protected $fillable = [
        'id',
        'razonSocial',
    ];

    public $timestamps = false;

    public function usuarios()
    {
        return $this->hasMany(User::class, 'empresa_id', 'id');
    }
    
    public function ReporteVentas()
    {
        return $this->hasMany(ReporteVenta::class, 'idCliente', 'id');
    }
}
