<?php

namespace App\Filament\SuratRekomendasi\Resources;

use App\Filament\SuratRekomendasi\Resources\SuratRekomendasiResource\Pages;
use App\Models\Departemen;
use App\Models\Petugas;
use App\Models\SuratRekomendasi;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
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
    // Form (judul & sub-judul ada di halaman Create/Edit).
    // Grid responsive: 1 kolom di HP, 2-3 kolom mulai layar md.
    // -------------------------------------------------------------------------

    public static function form(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Informasi Petugas & Instansi')
                ->columns(['default' => 1, 'md' => 2])
                ->schema([
                    TextInput::make('nomor_surat')
                        ->label('Nomor Surat')
                        ->placeholder('Contoh: 001/IT-SGG/X/2026')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->columnSpanFull(),

                    // Jabatan terisi otomatis dari master petugas (model juga menyalinnya saat simpan)
                    Select::make('petugas_id')
                        ->label('Nama Petugas')
                        ->relationship('petugas', 'nama')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('jabatan_petugas', Petugas::find($state)?->jabatan))
                        ->createOptionForm([
                            TextInput::make('nama')
                                ->label('Nama Petugas')
                                ->required()
                                ->maxLength(255)
                                ->unique('petugas', 'nama'),
                            TextInput::make('jabatan')
                                ->label('Jabatan')
                                ->placeholder('Contoh: IT Support PT. Syifa Global Group')
                                ->required()
                                ->maxLength(255),
                        ])
                        ->createOptionAction(fn (Action $action) => self::compactModal($action, 'Tambah Petugas')),

                    TextInput::make('jabatan_petugas')
                        ->label('Jabatan Petugas')
                        ->placeholder('Terisi otomatis setelah memilih petugas')
                        ->readOnly()
                        ->dehydrated(false),

                    Select::make('perusahaan_id')
                        ->label('Perusahaan')
                        ->relationship('perusahaan', 'nama')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn (Set $set) => $set('departemen_id', null))
                        ->createOptionForm([
                            TextInput::make('nama')
                                ->label('Nama Perusahaan')
                                ->required()
                                ->maxLength(255)
                                ->unique('perusahaan', 'nama'),
                        ])
                        ->createOptionAction(fn (Action $action) => self::compactModal($action, 'Tambah Perusahaan')),

                    // Pilihan & departemen baru selalu terikat ke perusahaan yang dipilih
                    Select::make('departemen_id')
                        ->label('Instalasi / Departemen')
                        ->relationship(
                            'departemen',
                            'nama',
                            modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('perusahaan_id', $get('perusahaan_id')),
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        ->disabled(fn (Get $get): bool => blank($get('perusahaan_id')))
                        ->placeholder('Pilih perusahaan terlebih dahulu')
                        ->rules(fn (Get $get): array => [
                            Rule::exists('departemen', 'id')->where('perusahaan_id', $get('perusahaan_id')),
                        ])
                        ->createOptionForm([
                            TextInput::make('nama')
                                ->label('Nama Instalasi / Departemen')
                                ->required()
                                ->maxLength(255),
                        ])
                        ->createOptionUsing(fn (array $data, Get $get): int => Departemen::firstOrCreate([
                            'perusahaan_id' => $get('perusahaan_id'),
                            'nama' => $data['nama'],
                        ])->getKey())
                        ->createOptionAction(fn (Action $action) => self::compactModal($action, 'Tambah Departemen')),

                    TextInput::make('nama_pengguna')
                        ->label('Nama Pengguna')
                        ->required()
                        ->maxLength(255),
                ]),

            Section::make('Detail Perangkat')
                ->columns(['default' => 1, 'md' => 3])
                ->schema([
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

                    Textarea::make('spesifikasi_perangkat')
                        ->label('Spesifikasi Perangkat')
                        ->rows(3)
                        ->columnSpanFull(),

                    Textarea::make('keterangan_rekomendasi')
                        ->label('Keterangan Rekomendasi')
                        ->rows(3)
                        ->required()
                        ->columnSpanFull(),
                ]),

            Section::make('Dokumentasi & Legalisasi')
                ->columns(['default' => 1, 'md' => 2])
                ->schema([
                    FileUpload::make('dokumentasi_perangkat')
                        ->label('Dokumentasi Perangkat')
                        ->image()
                        ->disk('public')
                        ->directory('surat-rekomendasi')
                        ->maxSize(2048)
                        ->columnSpanFull(),

                    // Nama & NIP penandatangan tidak diisi lewat form: memakai default kolom di migration
                    DatePicker::make('tanggal_surat')
                        ->label('Tanggal Surat')
                        ->default(now())
                        ->required(),
                ]),

        ])->columns(1);
    }

    // Modal tambah data master: lebar 'lg' agar pas di layar HP
    protected static function compactModal(Action $action, string $heading): Action
    {
        return $action
            ->modalHeading($heading)
            ->modalWidth('lg')
            ->modalSubmitActionLabel('Simpan');
    }

    // -------------------------------------------------------------------------
    // Table: kolom utama selalu tampil, kolom sekunder bisa dimunculkan via toggle
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
                    ->searchable()
                    ->limit(30),

                TextColumn::make('nama_perusahaan')
                    ->label('Perusahaan')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('nama_departemen')
                    ->label('Instalasi / Departemen')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('nama_pengguna')
                    ->label('Pengguna')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('jenis_perangkat')
                    ->label('Jenis Perangkat')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tanggal_surat')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
