<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

// Instalasi/departemen; satu nama boleh dimiliki beberapa perusahaan (lihat perusahaan())
enum DepartemenEnum: string implements HasLabel
{
    case TEKNOLOGI_INFORMASI = 'Teknologi Informasi';
    case KEUANGAN = 'Keuangan';
    case SDM = 'Sumber Daya Manusia';
    case INSTALASI_GAWAT_DARURAT = 'Instalasi Gawat Darurat';
    case INSTALASI_RAWAT_INAP = 'Instalasi Rawat Inap';
    case INSTALASI_RAWAT_JALAN = 'Instalasi Rawat Jalan';
    case INSTALASI_FARMASI = 'Instalasi Farmasi';
    case INSTALASI_LABORATORIUM = 'Instalasi Laboratorium';
    case INSTALASI_RADIOLOGI = 'Instalasi Radiologi';
    case REKAM_MEDIS = 'Rekam Medis';

    public function getLabel(): string
    {
        return $this->value;
    }

    /**
     * @return list<PerusahaanEnum>
     */
    public function perusahaan(): array
    {
        return match ($this) {
            self::TEKNOLOGI_INFORMASI, self::KEUANGAN, self::SDM => [
                PerusahaanEnum::SYIFA_GLOBAL_GROUP,
                PerusahaanEnum::RSU_SYIFA_MEDIKA_BANJARBARU,
            ],
            default => [PerusahaanEnum::RSU_SYIFA_MEDIKA_BANJARBARU],
        };
    }

    /**
     * Opsi departemen milik satu perusahaan, untuk dependent select.
     *
     * @return array<string, string>
     */
    public static function optionsFor(PerusahaanEnum|string|null $perusahaan): array
    {
        $perusahaan = is_string($perusahaan) ? PerusahaanEnum::tryFrom($perusahaan) : $perusahaan;

        if ($perusahaan === null) {
            return [];
        }

        return collect(self::cases())
            ->filter(fn (self $departemen): bool => in_array($perusahaan, $departemen->perusahaan(), true))
            ->mapWithKeys(fn (self $departemen): array => [$departemen->value => $departemen->getLabel()])
            ->all();
    }
}
