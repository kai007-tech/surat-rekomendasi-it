<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

// Petugas IT pembuat surat; nilai = nama yang disimpan & dicetak di PDF
enum PetugasEnum: string implements HasLabel
{
    case MUHAMMAD_FERDY_PRIAMJAYA = 'Muhammad Ferdy Priamjaya';
    case MUHAMMAD_JAKA_SAADILLAH = 'Muhammad Jaka Saadillah';

    public function getLabel(): string
    {
        return $this->value;
    }

    // Jabatan otomatis mengikuti nama petugas
    public function jabatan(): string
    {
        return match ($this) {
            self::MUHAMMAD_FERDY_PRIAMJAYA => 'IT Network PT. Syifa Global Group',
            self::MUHAMMAD_JAKA_SAADILLAH => 'IT Support PT. Syifa Global Group',
        };
    }
}
