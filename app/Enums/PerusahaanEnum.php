<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

// Perusahaan pemohon perangkat; nilai = nama yang disimpan & dicetak di PDF
enum PerusahaanEnum: string implements HasLabel
{
    case SYIFA_GLOBAL_GROUP = 'PT. Syifa Global Group';
    case RSU_SYIFA_MEDIKA_BANJARBARU = 'RSU Syifa Medika Banjarbaru';

    public function getLabel(): string
    {
        return $this->value;
    }
}
