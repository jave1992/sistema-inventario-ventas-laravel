<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'serie',
        'correlativo',
        'razon_social',
        'ruc',
        'total',
        'fecha_envio',
        'estado',
        'path_pdf',
        'path_xml',
        'path_cdr',
        'hash',
        'sale_id'
    ];

    protected $casts = [
        'fecha_envio' => 'datetime'
    ];

    // Relación uno a muchos inversa
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    // Relación uno a muchos
    public function note()
    {
        return $this->hasMany(Note::class);
    }
}
