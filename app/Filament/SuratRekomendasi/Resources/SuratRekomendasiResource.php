<?php

namespace App\Filament\SuratRekomendasi\Resources;

use App\Enums\DepartemenEnum;
use App\Enums\PerusahaanEnum;
use App\Enums\PetugasEnum;
use App\Filament\SuratRekomendasi\Resources\SuratRekomendasiResource\Pages;
use App\Models\SuratRekomendasi;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SuratRekomendasiResource extends Resource
{
    protected static ?string $model = SuratRekomendasi::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Surat Rekomendasi';

    protected static ?string $modelLabel = 'Surat Rekomendasi';

    protected static ?string $pluralModelLabel = 'Daftar Surat Rekomendasi';

    protected static ?string $slug = 'surat-rekomendasi';

    // -------------------------------------------------------------------------
    // Form (judul & sub-judul ada di halaman Create/Edit)
    // -------------------------------------------------------------------------

    public static function form(Schema $schema): Schema
    {
        return $schema->components([

            TextInput::make('nomor_surat')
                ->label('Nomor Surat')
                ->placeholder('Contoh: 001/IT-SGG/X/2026')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),

            self::dashedDivider(),

            // Jabatan terisi otomatis dari enum saat petugas dipilih (model juga mengisinya saat simpan)
            Grid::make(2)->schema([
                Select::make('nama_petugas')
                    ->label('Nama Petugas')
                    ->options(PetugasEnum::class)
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set, mixed $state) => $set('jabatan_petugas', self::toEnum(PetugasEnum::class, $state)?->jabatan())),

                TextInput::make('jabatan_petugas')
                    ->label('Jabatan Petugas')
                    ->placeholder('Terisi otomatis setelah memilih petugas')
                    ->readOnly()
                    ->dehydrated(false),
            ]),

            self::dashedDivider(),

            // Pilihan departemen tersaring sesuai perusahaan
            Grid::make(2)->schema([
                Select::make('nama_perusahaan')
                    ->label('Perusahaan')
                    ->options(PerusahaanEnum::class)
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('nama_departemen', null)),

                Select::make('nama_departemen')
                    ->label('Instalasi / Departemen')
                    ->options(fn (Get $get): array => DepartemenEnum::optionsFor(self::toEnum(PerusahaanEnum::class, $get('nama_perusahaan'))))
                    ->disabled(fn (Get $get): bool => blank($get('nama_perusahaan')))
                    ->placeholder('Pilih perusahaan terlebih dahulu')
                    ->required(),

                TextInput::make('nama_pengguna')
                    ->label('Nama Pengguna')
                    ->required()
                    ->maxLength(255),
            ]),

            self::dashedDivider(),

            Grid::make(3)->schema([
                TextInput::make('jenis_perangkat')
                    ->label('Jenis Perangkat')
                    ->placeholder('Contoh: Laptop')
                    ->required()
                    ->maxLength(255),

                TextInput::make('merk_perangkat')
                    ->label('Merk Perangkat')
                    ->required()
                    ->maxLength(255),

                TextInput::make('model_perangkat')
                    ->label('Model Perangkat')
                    ->placeholder('Kosongkan jika tidak ada')
                    ->maxLength(255),
            ]),

            Textarea::make('spesifikasi_perangkat')
                ->label('Spesifikasi Perangkat')
                ->rows(3)
                ->columnSpanFull(),

            Textarea::make('keterangan_rekomendasi')
                ->label('Keterangan Rekomendasi')
                ->rows(3)
                ->required()
                ->columnSpanFull(),

            self::dashedDivider(),

            FileUpload::make('dokumentasi_perangkat')
                ->label('Dokumentasi Perangkat')
                ->image()
                ->disk('public')
                ->directory('surat-rekomendasi')
                ->maxSize(2048)
                ->columnSpanFull(),

        ])->columns(1);
    }

    // Garis putus-putus pemisah antar seksi form (CSS: public/css/surat-rekomendasi.css)
    protected static function dashedDivider(): Html
    {
        return Html::make('<hr class="sr-dashed-divider">');
    }

    /**
     * State Select bisa berupa instance enum (data lama) atau string (pilihan baru).
     *
     * @template T of BackedEnum
     *
     * @param  class-string<T>  $enum
     * @return T|null
     */
    protected static function toEnum(string $enum, mixed $state): ?BackedEnum
    {
        if ($state instanceof $enum) {
            return $state;
        }

        return blank($state) ? null : $enum::tryFrom((string) $state);
    }

    // -------------------------------------------------------------------------
    // Table
    // -------------------------------------------------------------------------

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_surat')
                    ->label('Nomor Surat')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nama_petugas')
                    ->label('Nama Petugas')
                    ->searchable(),

                TextColumn::make('nama_perusahaan')
                    ->label('Perusahaan')
                    ->searchable(),

                TextColumn::make('jenis_perangkat')
                    ->label('Jenis Perangkat')
                    ->searchable(),

                TextColumn::make('tanggal_surat')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('tanggal_surat', 'desc')
            ->recordActions([
                Action::make('downloadPdf')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn (SuratRekomendasi $record): StreamedResponse => self::downloadPdf($record)),

                EditAction::make()
                    ->label('Ubah'),

                DeleteAction::make()
                    ->label('Hapus')
                    ->modalHeading('Hapus Surat Rekomendasi')
                    ->modalDescription('Yakin ingin menghapus surat rekomendasi ini?')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->modalCancelActionLabel('Batal'),
            ]);
    }

    public static function downloadPdf(SuratRekomendasi $surat): StreamedResponse
    {
        $pdf = Pdf::loadView('pdf.surat-rekomendasi', ['record' => $surat])->setPaper('a4');

        return response()->streamDownload(function () use ($pdf): void {
            echo $pdf->output();
        }, $surat->pdfFileName(), ['Content-Type' => 'application/pdf']);
    }

    // -------------------------------------------------------------------------
    // Pages
    // -------------------------------------------------------------------------

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuratRekomendasi::route('/'),
            'create' => Pages\CreateSuratRekomendasi::route('/create'),
            'edit' => Pages\EditSuratRekomendasi::route('/{record}/edit'),
        ];
    }
}
