<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Rekomendasi IT — {{ $record->nomor_surat }}</title>
    {{-- Dompdf hanya membaca CSS di dokumen ini; tabel dipakai murni sebagai grid tanpa border --}}
    <style>
        @page { margin: 34px 40px; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 9.5pt; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; border: none; }
        td { border: none; padding: 0; vertical-align: top; }

        /* Header */
        .title { font-size: 16pt; font-weight: bold; color: #0f172a; line-height: 1.1; margin: 0; }
        .subtitle { font-size: 9pt; font-weight: normal; color: #64748b; margin: 2px 0 10px 0; }
        .nomor-label { font-size: 9.5pt; font-weight: bold; color: #0f172a; }
        .nomor-value { font-size: 11pt; font-weight: bold; color: #0f172a; margin-top: 3px; }
        .logo { text-align: right; }
        .logo img { width: 160px; }

        /* Divider */
        .divider-dashed { border-bottom: 2px dashed #a0a0a0; margin: 10px 0; }
        .divider-pink { border-bottom: 3.5px solid #e6007e; margin: 10px 0; }

        /* Petugas & jabatan: label dan value navy seperti acuan */
        .petugas-label { font-size: 9.5pt; font-weight: bold; color: #1e3a8a; }
        .petugas-value { font-size: 10.5pt; font-weight: bold; color: #1e3a8a; margin-top: 3px; }

        /* Field umum */
        .label { font-size: 9.5pt; font-weight: bold; color: #1e293b; }
        .value { font-size: 9.5pt; font-weight: normal; color: #334155; line-height: 1.35; margin-top: 3px; }
        .field { padding-bottom: 14px; }

        /* Dokumentasi rata kiri */
        .doc-photo { max-width: 180px; max-height: 220px; margin-top: 4px; }

        /* Tanda tangan: garis pink di atas, teks rata tengah */
        .signature { margin-top: 70px; }
        .signature-line { border-bottom: 3.5px solid #e6007e; margin-bottom: 14px; }
        .signature-box { text-align: center; font-size: 9.5pt; font-weight: bold; color: #0f172a; }
        .signature-jabatan { margin-top: 8px; }
        .signature-space { height: 70px; }
        .signature-name { font-size: 10pt; color: #0f172a; }
    </style>
</head>
<body>
    {{-- HEADER: judul & nomor surat kiri, logo kanan --}}
    <table>
        <tr>
            <td>
                <h1 class="title">SURAT REKOMENDASI IT</h1>
                <p class="subtitle">Surat rekomendasi terkait <em>upgrade</em> atau pergantian perangkat</p>
                <div class="nomor-label">Nomor Surat</div>
                <div class="nomor-value">{{ $record->nomor_surat }}</div>
            </td>
            <td class="logo" width="35%">
                <img src="{{ public_path('images/logo-syifa.jpeg') }}" alt="Logo Syifa Global Group">
            </td>
        </tr>
    </table>

    <div class="divider-dashed"></div>

    {{-- PETUGAS & JABATAN --}}
    <table>
        <tr>
            <td width="40%">
                <div class="petugas-label">Nama Petugas</div>
                <div class="petugas-value">{{ $record->nama_petugas->value }}</div>
            </td>
            <td width="60%">
                <div class="petugas-label">Jabatan</div>
                <div class="petugas-value">{{ $record->jabatan_petugas }}</div>
            </td>
        </tr>
    </table>

    <div class="divider-pink"></div>

    {{-- PERUSAHAAN, DEPARTEMEN & PENGGUNA --}}
    <table>
        <tr>
            <td width="33%">
                <div class="label">Nama Perusahaan</div>
                <div class="value">{{ $record->nama_perusahaan->value }}</div>
            </td>
            <td width="34%">
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

    {{-- Dua kolom: kiri (jenis, merk, spesifikasi, dokumentasi), kanan (model, keterangan, TTD) --}}
    <table>
        <tr>
            <td width="50%">
                <table>
                    <tr>
                        <td width="50%" class="field">
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
                    <img class="doc-photo" src="{{ public_path('storage/' . $record->dokumentasi_perangkat) }}" alt="Dokumentasi perangkat">
                @else
                    <div class="value">-</div>
                @endif
            </td>
            <td width="50%">
                <div class="field">
                    <div class="label">Model Perangkat</div>
                    <div class="value">{{ $record->model_perangkat ?: '-' }}</div>
                </div>

                <div class="label">Keterangan Rekomendasi</div>
                <div class="value">{!! nl2br(e($record->keterangan_rekomendasi)) !!}</div>

                {{-- Area kosong untuk TTD basah & stempel --}}
                <div class="signature">
                    <div class="signature-line"></div>
                    <div class="signature-box">
                        <div>{{ $record->tanggal_surat->locale('id')->translatedFormat('l, F d, Y') }}</div>
                        <div class="signature-jabatan">{{ $record->jabatan_penandatangan }}</div>
                        <div class="signature-space"></div>
                        <u><strong class="signature-name">{{ $record->nama_penandatangan }}</strong></u><br>
                        {{ $record->nip_penandatangan }}
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
