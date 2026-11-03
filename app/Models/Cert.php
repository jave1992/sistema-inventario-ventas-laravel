<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cert extends Model
{
    protected $fillable = [
        'path',
        'size',
        'certable_id',
        'certable_type'
    ];

    // Relación polimórfica
    public function certable()
    {
        return $this->morphTo();
    }
}
