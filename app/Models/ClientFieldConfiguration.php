<?php

// app/Models/ClientFieldConfiguration.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientFieldConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'cod1_is_visible', 'cod1_label',
        'cod2_is_visible', 'cod2_label',
        'cod3_is_visible', 'cod3_label',
        'cod4_is_visible', 'cod4_label',
    ];

    protected $casts = [
        'cod1_is_visible' => 'boolean',
        'cod2_is_visible' => 'boolean',
        'cod3_is_visible' => 'boolean',
        'cod4_is_visible' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Empresa::class); // Asegúrate de que tu modelo Cliente se llame 'Client'
    }
}
