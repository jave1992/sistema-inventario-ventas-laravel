<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'razon_social',
        'ruc',
        'direccion',
        'sol_user',
        'sol_pass',
        'client_id',
        'client_secret',
        'production',
        'user_id',
    ];

    // Accesores
    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->images->count() ? Storage::url($this->images->first()->path) : Storage::url('logos/no-image.jpeg')
        );
    }

    // Accesores
    protected function cert(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->certs->count() ? Storage::url($this->certs->first()->path) : Storage::url('certs/no-cert.pem')
        );
    }

    // Relacion uno a muchos inversa
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación polimórfica
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    // Relación polimórfica
    public function certs()
    {
        return $this->morphMany(Cert::class, 'certable');
    }
}
