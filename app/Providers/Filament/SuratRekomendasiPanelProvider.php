<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SuratRekomendasiPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('surat_rekomendasi')
            ->path('surat_rekomendasi')
            ->login()                        // Login memakai tabel users yang sama dengan panel admin
            ->brandName('Surat Rekomendasi IT')
            ->colors([
                'primary' => Color::hex('#008A45'),
            ])
            ->discoverResources(in: app_path('Filament/SuratRekomendasi/Resources'), for: 'App\Filament\SuratRekomendasi\Resources')
            ->discoverPages(in: app_path('Filament/SuratRekomendasi/Pages'), for: 'App\Filament\SuratRekomendasi\Pages')
            ->pages([])                      // Tanpa dashboard: langsung ke daftar surat
            ->discoverWidgets(in: app_path('Filament/SuratRekomendasi/Widgets'), for: 'App\Filament\SuratRekomendasi\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
