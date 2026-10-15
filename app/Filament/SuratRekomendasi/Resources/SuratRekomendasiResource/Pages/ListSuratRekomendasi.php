<?php

namespace App\Filament\SuratRekomendasi\Resources\SuratRekomendasiResource\Pages;

use App\Filament\SuratRekomendasi\Resources\SuratRekomendasiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSuratRekomendasi extends ListRecords
{
    protected static string $resource = SuratRekomendasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Buat Surat Rekomendasi'),
        ];
    }
}
