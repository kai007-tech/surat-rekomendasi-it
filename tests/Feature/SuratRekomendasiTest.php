<?php

namespace Tests\Feature;

use App\Enums\DepartemenEnum;
use App\Enums\PerusahaanEnum;
use App\Enums\PetugasEnum;
use App\Filament\SuratRekomendasi\Resources\SuratRekomendasiResource\Pages\CreateSuratRekomendasi;
use App\Filament\SuratRekomendasi\Resources\SuratRekomendasiResource\Pages\ListSuratRekomendasi;
use App\Models\SuratRekomendasi;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class SuratRekomendasiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('surat_rekomendasi');
        $this->actingAs(User::factory()->create());
    }

    public function test_guest_is_redirected_to_panel_login(): void
    {
        auth()->logout();

        $this->get('/surat_rekomendasi/surat-rekomendasi')->assertRedirect('/surat_rekomendasi/login');
    }

    public function test_create_form_fills_jabatan_from_petugas_and_saves_surat(): void
    {
        Carbon::setTestNow('2026-09-15');

        Livewire::test(CreateSuratRekomendasi::class)
            ->fillForm(['nama_petugas' => PetugasEnum::MUHAMMAD_JAKA_SAADILLAH->value])
            ->assertSchemaStateSet(['jabatan_petugas' => 'IT Support PT. Syifa Global Group'])
            ->fillForm([
                'nomor_surat' => '001/IT-SGG/IX/2026',
                'nama_perusahaan' => PerusahaanEnum::RSU_SYIFA_MEDIKA_BANJARBARU->value,
                'nama_departemen' => DepartemenEnum::INSTALASI_FARMASI->value,
                'nama_pengguna' => 'Siti Aminah',
                'jenis_perangkat' => 'Laptop',
                'merk_perangkat' => 'Lenovo',
                'keterangan_rekomendasi' => 'Perangkat lama rusak dan perlu diganti.',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $surat = SuratRekomendasi::firstOrFail();
        $this->assertSame('IT Support PT. Syifa Global Group', $surat->jabatan_petugas);
        $this->assertSame('-', $surat->model_perangkat);
        $this->assertSame('2026-09-15', $surat->tanggal_surat->toDateString());
        $this->assertSame('Lutfi Salisa Setiawati, S.Kom', $surat->nama_penandatangan);
        $this->assertSame('01122016017', $surat->nip_penandatangan);
    }

    public function test_departemen_must_belong_to_selected_perusahaan(): void
    {
        // Instalasi Farmasi hanya ada di RSU, bukan di PT. Syifa Global Group
        Livewire::test(CreateSuratRekomendasi::class)
            ->fillForm([
                'nomor_surat' => '002/IT-SGG/IX/2026',
                'nama_petugas' => PetugasEnum::MUHAMMAD_FERDY_PRIAMJAYA->value,
                'nama_perusahaan' => PerusahaanEnum::SYIFA_GLOBAL_GROUP->value,
                'nama_departemen' => DepartemenEnum::INSTALASI_FARMASI->value,
                'nama_pengguna' => 'Budi',
                'jenis_perangkat' => 'Printer',
                'merk_perangkat' => 'Epson',
                'keterangan_rekomendasi' => 'Pengadaan baru.',
            ])
            ->call('create')
            ->assertHasFormErrors(['nama_departemen']);

        $this->assertDatabaseCount('surat_rekomendasi', 0);
    }

    public function test_download_pdf_action_streams_pdf_file(): void
    {
        $surat = SuratRekomendasi::factory()->create(['nomor_surat' => '003/IT-SGG/IX/2026']);

        Livewire::test(ListSuratRekomendasi::class)
            ->callAction(TestAction::make('downloadPdf')->table($surat))
            ->assertFileDownloaded('surat-rekomendasi-003-it-sgg-ix-2026.pdf');
    }

    public function test_pdf_template_shows_indonesian_date_and_signatory(): void
    {
        Carbon::setTestNow('2026-09-15');
        $surat = SuratRekomendasi::factory()->create()->fresh();

        $html = view('pdf.surat-rekomendasi', ['record' => $surat])->render();

        $this->assertStringContainsString('Selasa, September 15, 2026', $html);
        $this->assertStringContainsString('IT Supervisor PT. Syifa Global Group', $html);
        $this->assertStringContainsString('Lutfi Salisa Setiawati, S.Kom', $html);
        $this->assertStringContainsString('01122016017', $html);
        $this->assertStringContainsString($surat->jabatan_petugas, $html);
    }
}
