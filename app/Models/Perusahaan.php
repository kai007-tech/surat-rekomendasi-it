<?php

namespace App\Models;

use Database\Factories\PerusahaanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perusahaan extends Model
{
    /** @use HasFactory<PerusahaanFactory> */
    use HasFactory;

    protected $table = 'perusahaan';

    protected $fillable = ['nama'];

    public function departemen(): HasMany
    {
        return $this->hasMany(Departemen::class);
    }
}
