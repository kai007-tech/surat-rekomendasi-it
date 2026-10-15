<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departemen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained('perusahaan')->cascadeOnDelete();
            $table->string('nama');
            $table->timestamps();

            $table->unique(['perusahaan_id', 'nama']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departemen');
    }
};
