<?php

namespace App\Models;

use Database\Factories\PetugasFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Petugas extends Model
{
    /** @use HasFactory<PetugasFactory> */
    use HasFactory;

    protected $table = 'petugas';

    protected $fillable = ['nama', 'jabatan'];
}
