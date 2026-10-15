<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Rekomendasi IT — {{ $record->nomor_surat }}</title>

    {{-- Dompdf hanya membaca CSS di dokumen ini; tabel dipakai murni sebagai grid tanpa border --}}
    <style>
        @page { size: A4 portrait; margin: 34px 40px; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Helvetica, Arial, sans-serif; font-size: 9.5pt; color: #0b123b; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        td { padding: 0; vertical-align: top; }

        /* Watermark full A4: offset negatif menutup margin halaman; fixed tidak ikut alur konten */
        .watermark { position: fixed; top: -34px; left: -40px; width: 210mm; height: 297mm; z-index: -1000; opacity: 0.12; }

        /* Header */
        .title { font-size: 19pt; font-weight: bold; color: #080d36; line-height: 1.2; margin: 0 0 17px; }
        .subtitle { font-size: 10pt; color: #505466; margin: 0 0 17px; }
        .nomor-label { font-size: 11pt; font-weight: bold; color: #080d36; }
        .nomor-value { font-size: 12pt; font-weight: bold; color: #080d36; margin-top: 2px; }
        .logo-cell { text-align: right; }
        .logo { width: 165px; }

        /* Garis */
        .divider-dashed { border-bottom: 4px dashed #a0a0a0; margin: 19px 0 14px; }
        .divider-pink { border-bottom: 4px solid #e600d2; margin: 18px 0 22px; }

        /* Petugas & jabatan */
        .petugas-label { font-size: 11pt; font-weight: bold; color: #10177a; }
        .petugas-value { font-size: 11.5pt; font-weight: bold; color: #10177a; margin-top: 5px; }

        /* Field umum */
        .label { font-size: 11pt; font-weight: bold; color: #080d36; line-height: 1.25; }
        .value { font-size: 10.5pt; color: #515565; line-height: 1.25; margin-top: 4px; }
        .field { padding-bottom: 19px; }
        .gap-right { padding-right: 8px; }
        .device-table td { padding-bottom: 19px; padding-right: 8px; }
        .keterangan-label { margin-top: 14px; }
        .keterangan-text { line-height: 1.35; color: #515565; }

        /* Dokumentasi rata kiri */
        .doc-photo { display: block; max-width: 180px; max-height: 140px; width: auto; height: auto; object-fit: contain; margin-top: 4px; }

        /* Tanda tangan: terkunci di kanan bawah halaman, tidak ikut panjang keterangan */
        /* Dompdf: offset dihitung dari area konten (di dalam margin @page), jadi right: 0 = sejajar garis pink */
        .signature { position: absolute; bottom: 40px; right: 0; width: 48%; }
        .signature-line { border-bottom: 3.5px solid #e600d2; margin-bottom: 12px; }
        .signature-box { text-align: center; font-size: 9.5pt; font-weight: bold; color: #080808; line-height: 1.35; }
        .signature-date { margin-bottom: 4px; }
        .signature-space { height: 65px; } /* Ruang TTD basah / stempel */
    </style>
</head>
<body>
    @if(file_exists(public_path('images/logo-watermark.png')))
        <img class="watermark" src="{{ public_path('images/logo-watermark.png') }}" alt="">
    @endif

    {{-- HEADER: judul & nomor surat kiri, logo kanan --}}
    <table>
        <tr>
            <td width="65%" class="gap-right">
                <h1 class="title">SURAT REKOMENDASI IT</h1>
                <p class="subtitle">Surat rekomendasi terkait <em>upgrade</em> atau pergantian perangkat</p>
                <div class="nomor-label">Nomor Surat</div>
                <div class="nomor-value">{{ $record->nomor_surat }}</div>
            </td>
            <td width="35%" class="logo-cell">
                <img class="logo" src="{{ public_path('images/logo-syifa.jpeg') }}" alt="Logo Syifa Global Group">
            </td>
        </tr>
    </table>

    <div class="divider-dashed"></div>

    {{-- PETUGAS & JABATAN --}}
    <table>
        <tr>
            <td width="42%">
                <div class="petugas-label">Nama Petugas</div>
                <div class="petugas-value">{{ $record->nama_petugas }}</div>
            </td>
            <td width="58%">
                <div class="petugas-label">Jabatan</div>
                <div class="petugas-value">{{ $record->jabatan_petugas }}</div>
            </td>
        </tr>
    </table>

    <div class="divider-pink"></div>

    {{-- PERUSAHAAN, DEPARTEMEN & PENGGUNA --}}
    <table>
        <tr>
            <td width="34%" class="gap-right">
                <div class="label">Nama Perusahaan</div>
                <div class="value">{{ $record->nama_perusahaan }}</div>
            </td>
            <td width="33%" class="gap-right">
                <div class="label">Nama Instalasi/Departemen</div>
                <div class="value">{{ $record->nama_departemen }}</div>
            </td>
            <td width="33%">
                <div class="label">Nama Pengguna</div>
                <div class="value">{{ $record->nama_pengguna }}</div>
            </td>
        </tr>
    </table>

    <div class="divider-pink"></div>

    {{-- Kiri: jenis, merk, spesifikasi, dokumentasi. Kanan: model, keterangan, TTD --}}
    <table>
        <tr>
            <td width="50%" class="gap-right">
                <table class="device-table">
                    <tr>
                        <td width="50%">
                            <div class="label">Jenis Perangkat</div>
                            <div class="value">{{ $record->jenis_perangkat }}</div>
                        </td>
                        <td width="50%">
                            <div class="label">Merk Perangkat</div>
                            <div class="value">{{ $record->merk_perangkat }}</div>
                        </td>
                    </tr>
                </table>

                <div class="field">
                    <div class="label">Spesifikasi Perangkat</div>
                    <div class="value">{!! nl2br(e($record->spesifikasi_perangkat ?: '-')) !!}</div>
                </div>

                <div class="label">Dokumentasi Perangkat</div>
                @if($record->dokumentasi_perangkat)
                    <img class="doc-photo" src="{{ public_path('storage/'.$record->dokumentasi_perangkat) }}" alt="Dokumentasi perangkat">
                @else
                    <div class="value">-</div>
                @endif
            </td>
            <td width="50%">
                <div class="label">Model Perangkat</div>
                <div class="value">{{ $record->model_perangkat ?: '-' }}</div>

                <div class="label keterangan-label">Keterangan Rekomendasi</div>
                <div class="value keterangan-text">{!! nl2br(e($record->keterangan_rekomendasi)) !!}</div>
            </td>
        </tr>
    </table>

    {{-- Tanda tangan di luar tabel agar posisi absolute mengacu ke halaman --}}
    <div class="signature">
        <div class="signature-line"></div>
        <div class="signature-box">
            <div class="signature-date">{{ $record->tanggal_surat->locale('id')->translatedFormat('l, F d, Y') }}</div>
            <div>{{ $record->jabatan_penandatangan }}</div>
            <div class="signature-space"></div>
            <u>{{ $record->nama_penandatangan }}</u><br>
            {{ $record->nip_penandatangan }}
        </div>
    </div>
</body>
</html>
