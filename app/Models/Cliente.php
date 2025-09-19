<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'idcliente',
        'logo',
    ];

    // Relacionar este modelo con la vista_empresas
    public function empresa()
    {
        return $this->hasOne(Empresa::class, 'id', 'idcliente');
    }
}
