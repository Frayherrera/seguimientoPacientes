<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'descripcion',
        'es_activo',
    ];

    protected $casts = [
        'es_activo' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
