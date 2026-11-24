<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
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
        'invoice_id'
    ];

    protected $casts = [
        'fecha_envio' => 'datetime'
    ];

    // Relación uno a muchos inversa
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
