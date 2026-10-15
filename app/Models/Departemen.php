<?php

namespace App\Models;

use Database\Factories\DepartemenFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Departemen extends Model
{
    /** @use HasFactory<DepartemenFactory> */
    use HasFactory;

    protected $table = 'departemen';

    protected $fillable = ['perusahaan_id', 'nama'];

    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class);
    }
}
