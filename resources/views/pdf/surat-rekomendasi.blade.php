<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Rekomendasi IT — {{ $record->nomor_surat }}</title>

    {{-- Dompdf hanya membaca CSS di dokumen ini; tabel dipakai murni sebagai grid tanpa border --}}
    <style>
        /* Margin 0 agar watermark menutupi kertas penuh; jarak tepi diatur .content-wrapper */
        @page { size: A4 portrait; margin: 0; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Helvetica, Arial, sans-serif; font-size: 9.5pt; color: #0b123b; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        td { padding: 0; vertical-align: top; }
        h1, p { margin: 0; }

        /* Watermark full page: fixed tidak ikut alur konten, jadi tidak memicu halaman ke-2 */
        .watermark { position: fixed; top: 0; left: 0; width: 100%; height: 100%; text-align: center; z-index: -1; }
        .watermark img { width: 100%; margin-top: 150px; opacity: 0.07; }

        .content-wrapper { padding: 28px 36px; }

        /* Header */
        .title { font-size: 19pt; font-weight: bold; color: #080d36; line-height: 1.2; margin-bottom: 8px; }
        .subtitle { font-size: 10pt; color: #505466; margin-bottom: 8px; }
        .nomor-label { font-size: 11pt; font-weight: bold; color: #080d36; }
        .nomor-value { font-size: 12pt; font-weight: bold; color: #080d36; margin-top: 2px; }
        .logo-cell { text-align: right; }
        .logo { width: 165px; }

        /* Garis */
        .divider-dashed { border-bottom: 4px dashed #a0a0a0; margin: 6px 0; }
        .divider-pink { border-bottom: 4px solid #e600d2; margin: 6px 0; }

        /* Petugas & jabatan */
        .petugas-label { font-size: 11pt; font-weight: bold; color: #10177a; }
        .petugas-value { font-size: 11.5pt; font-weight: bold; color: #10177a; margin-top: 2px; }

        /* Field umum */
        .label { font-size: 11pt; font-weight: bold; color: #080d36; line-height: 1.25; }
        .value { font-size: 10.5pt; color: #515565; line-height: 1.25; margin-top: 2px; }
        .field { padding-bottom: 8px; }
        .gap-right { padding-right: 8px; }

        /* Dokumentasi rata kiri, tinggi dibatasi agar tetap 1 halaman */
        .doc-photo { display: block; max-width: 180px; max-height: 130px; margin-top: 4px; }

        /* Tanda tangan */
        .signature { margin-top: 22px; }
        .signature-line { border-bottom: 4px solid #e600d2; margin-bottom: 10px; }
        .signature-box { text-align: center; font-size: 10pt; font-weight: bold; color: #080808; line-height: 1.35; }
        .signature-date { margin-bottom: 6px; }
        .signature-space { height: 55px; }
    </style>
</head>
<body>
    @if(file_exists(public_path('images/logo-watermark.png')))
        <div class="watermark">
            <img src="{{ public_path('images/logo-watermark.png') }}" alt="">
        </div>
    @endif

    <div class="content-wrapper">
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
                    <div class="petugas-value">{{ $record->nama_petugas->value }}</div>
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
                    <div class="value">{{ $record->nama_perusahaan->value }}</div>
                </td>
                <td width="33%" class="gap-right">
                    <div class="label">Nama Instalasi/Departemen</div>
                    <div class="value">{{ $record->nama_departemen->value }}</div>
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
                    <table>
                        <tr>
                            <td width="50%" class="field gap-right">
                                <div class="label">Jenis Perangkat</div>
                                <div class="value">{{ $record->jenis_perangkat }}</div>
                            </td>
                            <td width="50%" class="field">
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
                    <div class="field">
                        <div class="label">Model Perangkat</div>
                        <div class="value">{{ $record->model_perangkat ?: '-' }}</div>
                    </div>

                    <div class="field">
                        <div class="label">Keterangan Rekomendasi</div>
                        <div class="value">{!! nl2br(e($record->keterangan_rekomendasi)) !!}</div>
                    </div>

                    {{-- Area kosong untuk TTD basah & stempel --}}
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
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
