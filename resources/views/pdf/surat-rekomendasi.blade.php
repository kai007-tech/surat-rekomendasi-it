<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Rekomendasi IT — {{ $surat->nomor_surat }}</title>
    {{-- Dompdf hanya membaca CSS di dokumen ini --}}
    <style>
        @page { margin: 32px 40px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1f2937; }
        table { width: 100%; border-collapse: collapse; }

        .header td { vertical-align: middle; }
        .header-logo { text-align: right; }
        .header-logo img { height: 60px; }
        .title { font-size: 18px; font-weight: bold; letter-spacing: 0.5px; margin: 0; }
        .subtitle { font-size: 11px; color: #6b7280; margin: 4px 0 0; }
        .divider { border: 0; border-top: 3px solid #D946EF; margin: 12px 0 18px; }

        .section-title { font-size: 12px; font-weight: bold; background: #f3f4f6; padding: 6px 8px; margin: 14px 0 0; }
        .detail td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        .detail .label { width: 32%; color: #4b5563; }
        .detail .colon { width: 2%; }

        .photo { text-align: center; padding: 10px 0; }
        .photo img { max-width: 320px; max-height: 240px; }

        .signature { margin-top: 32px; }
        .signature td { vertical-align: top; }
        .signature-box { width: 45%; text-align: center; }
        .signature-space { height: 70px; }
        .signature-name { font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>
    {{-- Header: judul kiri, logo kanan --}}
    <table class="header">
        <tr>
            <td>
                <p class="title">SURAT REKOMENDASI IT</p>
                <p class="subtitle">Surat rekomendasi terkait pengadaan perangkat IT</p>
            </td>
            <td class="header-logo">
                <img src="{{ public_path('images/logo-syifa.jpeg') }}" alt="Logo Syifa Global Group">
            </td>
        </tr>
    </table>
    <hr class="divider">

    <table class="detail">
        <tr><td class="label">Nomor Surat</td><td class="colon">:</td><td>{{ $surat->nomor_surat }}</td></tr>
        <tr><td class="label">Nama Petugas</td><td class="colon">:</td><td>{{ $surat->nama_petugas->value }}</td></tr>
        <tr><td class="label">Jabatan Petugas</td><td class="colon">:</td><td>{{ $surat->jabatan_petugas }}</td></tr>
        <tr><td class="label">Perusahaan</td><td class="colon">:</td><td>{{ $surat->nama_perusahaan->value }}</td></tr>
        <tr><td class="label">Instalasi / Departemen</td><td class="colon">:</td><td>{{ $surat->nama_departemen->value }}</td></tr>
        <tr><td class="label">Nama Pengguna</td><td class="colon">:</td><td>{{ $surat->nama_pengguna }}</td></tr>
    </table>

    <p class="section-title">Detail Perangkat</p>
    <table class="detail">
        <tr><td class="label">Jenis Perangkat</td><td class="colon">:</td><td>{{ $surat->jenis_perangkat }}</td></tr>
        <tr><td class="label">Merk Perangkat</td><td class="colon">:</td><td>{{ $surat->merk_perangkat }}</td></tr>
        <tr><td class="label">Model Perangkat</td><td class="colon">:</td><td>{{ $surat->model_perangkat }}</td></tr>
        <tr><td class="label">Spesifikasi</td><td class="colon">:</td><td>{!! nl2br(e($surat->spesifikasi_perangkat ?: '-')) !!}</td></tr>
        <tr><td class="label">Keterangan Rekomendasi</td><td class="colon">:</td><td>{!! nl2br(e($surat->keterangan_rekomendasi)) !!}</td></tr>
    </table>

    @if($surat->dokumentasi_perangkat)
        <p class="section-title">Foto Dokumentasi</p>
        <table>
            <tr>
                <td class="photo">
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->path($surat->dokumentasi_perangkat) }}" alt="Dokumentasi perangkat">
                </td>
            </tr>
        </table>
    @endif

    {{-- Tanda tangan basah & stempel di kanan bawah --}}
    <table class="signature">
        <tr>
            <td></td>
            <td class="signature-box">
                <div>{{ $surat->tanggal_surat->locale('id')->translatedFormat('l, j F Y') }}</div>
                <div>{{ $surat->jabatan_penandatangan }}</div>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $surat->nama_penandatangan }}</div>
                <div>NIP: {{ $surat->nip_penandatangan }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
