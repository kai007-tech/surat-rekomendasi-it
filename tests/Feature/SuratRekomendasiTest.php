<?php

namespace Tests\Feature;

use App\Filament\SuratRekomendasi\Resources\SuratRekomendasiResource\Pages\CreateSuratRekomendasi;
use App\Filament\SuratRekomendasi\Resources\SuratRekomendasiResource\Pages\ListSuratRekomendasi;
use App\Models\Departemen;
use App\Models\Perusahaan;
use App\Models\Petugas;
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

    /**
     * @return array<string, mixed>
     */
    private function validFormData(Departemen $departemen, Petugas $petugas): array
    {
        return [
            'nomor_surat' => '001/IT-SGG/IX/2026',
            'petugas_id' => $petugas->id,
            'perusahaan_id' => $departemen->perusahaan_id,
            'departemen_id' => $departemen->id,
            'nama_pengguna' => 'Siti Aminah',
            'jenis_perangkat' => 'Laptop',
            'merk_perangkat' => 'Lenovo',
            'keterangan_rekomendasi' => 'Perangkat lama rusak dan perlu diganti.',
        ];
    }

    public function test_guest_is_redirected_to_panel_login(): void
    {
        auth()->logout();

        $this->get('/surat_rekomendasi/surat-rekomendasi')->assertRedirect('/surat_rekomendasi/login');
    }

    public function test_create_form_fills_jabatan_from_master_and_saves_snapshot(): void
    {
        Carbon::setTestNow('2026-09-15');
        $petugas = Petugas::factory()->create(['nama' => 'Budi Santoso', 'jabatan' => 'IT Support PT. Syifa Global Group']);
        $departemen = Departemen::factory()->create();

        Livewire::test(CreateSuratRekomendasi::class)
            ->fillForm(['petugas_id' => $petugas->id])
            ->assertSchemaStateSet(['jabatan_petugas' => 'IT Support PT. Syifa Global Group'])
            ->fillForm($this->validFormData($departemen, $petugas))
            ->call('create')
            ->assertHasNoFormErrors();

        $surat = SuratRekomendasi::firstOrFail();
        $this->assertSame('Budi Santoso', $surat->nama_petugas);
        $this->assertSame('IT Support PT. Syifa Global Group', $surat->jabatan_petugas);
        $this->assertSame($departemen->perusahaan->nama, $surat->nama_perusahaan);
        $this->assertSame($departemen->nama, $surat->nama_departemen);
        $this->assertSame('-', $surat->model_perangkat);
        $this->assertSame('2026-09-15', $surat->tanggal_surat->toDateString());
        $this->assertSame('Lutfi Salisa Setiawati, S.Kom', $surat->nama_penandatangan);
        $this->assertSame('01122016017', $surat->nip_penandatangan);

        // Surat lama tidak ikut berubah saat data master diganti
        $petugas->update(['jabatan' => 'IT Supervisor']);
        $this->assertSame('IT Support PT. Syifa Global Group', $surat->fresh()->jabatan_petugas);
    }

    public function test_departemen_must_belong_to_selected_perusahaan(): void
    {
        $departemenLain = Departemen::factory()->create();
        $perusahaan = Perusahaan::factory()->create();

        Livewire::test(CreateSuratRekomendasi::class)
            ->fillForm([
                ...$this->validFormData($departemenLain, Petugas::factory()->create()),
                'perusahaan_id' => $perusahaan->id,
                'departemen_id' => $departemenLain->id,
            ])
            ->call('create')
            ->assertHasFormErrors(['departemen_id']);

        $this->assertDatabaseCount('surat_rekomendasi', 0);
    }

    public function test_new_departemen_from_modal_is_attached_to_selected_perusahaan(): void
    {
        $perusahaan = Perusahaan::factory()->create();

        Livewire::test(CreateSuratRekomendasi::class)
            ->fillForm(['perusahaan_id' => $perusahaan->id])
            ->callAction(
                TestAction::make('createOption')->schemaComponent('departemen_id'),
                ['nama' => 'Instalasi Farmasi'],
            )
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('departemen', ['perusahaan_id' => $perusahaan->id, 'nama' => 'Instalasi Farmasi']);
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
        $this->assertStringContainsString($surat->nama_petugas, $html);
        $this->assertStringContainsString($surat->nama_departemen, $html);
    }
}
