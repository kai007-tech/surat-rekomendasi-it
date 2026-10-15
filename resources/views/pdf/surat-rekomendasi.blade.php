<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Rekomendasi IT — {{ $record->nomor_surat }}</title>
    {{-- Dompdf hanya membaca CSS di dokumen ini; tabel dipakai murni sebagai grid tanpa border --}}
    <style>
        @page { margin: 36px 44px; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; border: none; }
        td { border: none; padding: 0; vertical-align: top; }

        .title { font-size: 18pt; font-weight: bold; color: #0f172a; margin: 0; }
        .subtitle { font-size: 9pt; color: #64748b; margin: 2px 0 10px 0; }
        .nomor-label { font-weight: bold; font-size: 10pt; color: #0f172a; }
        .nomor-value { font-weight: bold; font-size: 11pt; color: #0f172a; }
        .logo { text-align: right; }
        .logo img { width: 140px; }

        .divider-dashed { border-bottom: 2px dashed #a0a0a0; margin: 12px 0; }
        .divider-pink { border-bottom: 3.5px solid #e6007e; margin: 12px 0; }

        .label-navy { color: #1e3a8a; font-weight: bold; font-size: 10pt; }
        .value-strong { color: #0f172a; font-size: 11pt; font-weight: bold; }
        .label { font-weight: bold; color: #0f172a; }
        .value { color: #334155; }
        .row-gap td { padding-bottom: 12px; }

        .doc-title { margin-top: 14px; font-weight: bold; color: #0f172a; }
        .doc-photo { width: 180px; max-height: 240px; margin-top: 6px; }
        .keterangan { font-size: 9.5pt; line-height: 1.45; text-align: justify; color: #334155; margin-top: 4px; }
        .col-gap { padding-right: 16px; }

        .signature { margin-top: 40px; }
        .signature-line { border-bottom: 3.5px solid #e6007e; margin-bottom: 12px; }
        .signature-box { text-align: center; }
        .signature-space { height: 70px; }
    </style>
</head>
<body>
    {{-- HEADER: judul & nomor surat kiri, logo kanan --}}
    <table>
        <tr>
            <td>
                <h1 class="title">SURAT REKOMENDASI IT</h1>
                <p class="subtitle">Surat rekomendasi terkait upgrade atau pergantian perangkat</p>
                <div class="nomor-label">Nomor Surat</div>
                <div class="nomor-value">{{ $record->nomor_surat }}</div>
            </td>
            <td class="logo">
                <img src="{{ public_path('images/logo-syifa.jpeg') }}" alt="Logo Syifa Global Group">
            </td>
        </tr>
    </table>

    <div class="divider-dashed"></div>

    {{-- PETUGAS & JABATAN --}}
    <table>
        <tr>
            <td width="50%">
                <span class="label-navy">Nama Petugas</span><br>
                <span class="value-strong">{{ $record->nama_petugas->value }}</span>
            </td>
            <td width="50%">
                <span class="label-navy">Jabatan</span><br>
                <span class="value-strong">{{ $record->jabatan_petugas }}</span>
            </td>
        </tr>
    </table>

    <div class="divider-pink"></div>

    {{-- PERUSAHAAN, DEPARTEMEN & PENGGUNA --}}
    <table>
        <tr>
            <td width="35%" class="col-gap">
                <span class="label">Nama Perusahaan</span><br>
                <span class="value">{{ $record->nama_perusahaan->value }}</span>
            </td>
            <td width="35%" class="col-gap">
                <span class="label">Nama Instalasi/Departemen</span><br>
                <span class="value">{{ $record->nama_departemen->value }}</span>
            </td>
            <td width="30%">
                <span class="label">Nama Pengguna</span><br>
                <span class="value">{{ $record->nama_pengguna }}</span>
            </td>
        </tr>
    </table>

    <div class="divider-pink"></div>

    {{-- PERANGKAT --}}
    <table>
        <tr class="row-gap">
            <td width="33%" class="col-gap">
                <span class="label">Jenis Perangkat</span><br>
                <span class="value">{{ $record->jenis_perangkat }}</span>
            </td>
            <td width="33%" class="col-gap">
                <span class="label">Merk Perangkat</span><br>
                <span class="value">{{ $record->merk_perangkat }}</span>
            </td>
            <td width="34%">
                <span class="label">Model Perangkat</span><br>
                <span class="value">{{ $record->model_perangkat ?: '-' }}</span>
            </td>
        </tr>
    </table>

    {{-- SPESIFIKASI & DOKUMENTASI (kiri), KETERANGAN (kanan) --}}
    <table>
        <tr>
            <td width="45%" class="col-gap">
                <span class="label">Spesifikasi Perangkat</span><br>
                <span class="value">{!! nl2br(e($record->spesifikasi_perangkat ?: '-')) !!}</span>

                <div class="doc-title">Dokumentasi Perangkat</div>
                @if($record->dokumentasi_perangkat)
                    <img class="doc-photo" src="{{ public_path('storage/' . $record->dokumentasi_perangkat) }}" alt="Dokumentasi perangkat">
                @endif
            </td>
            <td width="55%">
                <span class="label">Keterangan Rekomendasi</span><br>
                <div class="keterangan">{!! nl2br(e($record->keterangan_rekomendasi)) !!}</div>
            </td>
        </tr>
    </table>

    {{-- TANDA TANGAN (kanan bawah): area kosong untuk TTD basah & stempel --}}
    <table class="signature">
        <tr>
            <td width="55%"></td>
            <td width="45%">
                <div class="signature-line"></div>
                <div class="signature-box">
                    <div>{{ $record->tanggal_surat->locale('id')->translatedFormat('l, F d, Y') }}</div>
                    <strong>{{ $record->jabatan_penandatangan }}</strong>
                    <div class="signature-space"></div>
                    <u><strong>{{ $record->nama_penandatangan }}</strong></u><br>
                    <strong>{{ $record->nip_penandatangan }}</strong>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
