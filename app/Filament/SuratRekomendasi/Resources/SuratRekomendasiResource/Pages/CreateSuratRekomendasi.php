<?php

namespace App\Filament\SuratRekomendasi\Resources\SuratRekomendasiResource\Pages;

use App\Filament\SuratRekomendasi\Resources\SuratRekomendasiResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSuratRekomendasi extends CreateRecord
{
    protected static string $resource = SuratRekomendasiResource::class;

    protected static ?string $title = 'SURAT REKOMENDASI IT';

    public function getSubheading(): ?string
    {
        return 'Surat rekomendasi terkait pengadaan perangkat IT';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
