<?php

namespace App\Filament\SuratRekomendasi\Resources\SuratRekomendasiResource\Pages;

use App\Filament\SuratRekomendasi\Resources\SuratRekomendasiResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSuratRekomendasi extends EditRecord
{
    protected static string $resource = SuratRekomendasiResource::class;

    protected static ?string $title = 'SURAT REKOMENDASI IT';

    public function getSubheading(): ?string
    {
        return 'Surat rekomendasi terkait pengadaan perangkat IT';
    }

    // Setelah Simpan kembali ke daftar surat
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadPdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn () => SuratRekomendasiResource::downloadPdf($this->getRecord())),

            DeleteAction::make()
                ->label('Hapus'),
        ];
    }
}
