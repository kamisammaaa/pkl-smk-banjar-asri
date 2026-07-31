<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Monitoring Kunjungan Industri</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 12px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #999;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .badge-rencana {
            color: #004085;
            background-color: #cce5ff;
            padding: 2px 4px;
            border-radius: 4px;
            font-size: 10px;
        }
        .badge-selesai {
            color: #155724;
            background-color: #d4edda;
            padding: 2px 4px;
            border-radius: 4px;
            font-size: 10px;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
        }
        .signature {
            margin-top: 50px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>LAPORAN MONITORING KUNJUNGAN INDUSTRI</h1>
        <p>SMK BANJAR ASRI</p>
    </div>

    @if(request('tanggal') || request('pembimbing_id') || request('perusahaan_id'))
    <div style="margin-bottom: 15px;">
        <strong>Filter Aktif:</strong><br>
        @if(request('tanggal')) - Tanggal: {{ \Carbon\Carbon::parse(request('tanggal'))->format('d/m/Y') }}<br> @endif
        @if(request('pembimbing_id')) - Pembimbing: {{ \App\Models\User::find(request('pembimbing_id'))->name ?? '-' }}<br> @endif
        @if(request('perusahaan_id')) - Perusahaan: {{ \App\Models\Perusahaan::find(request('perusahaan_id'))->nama ?? '-' }}<br> @endif
    </div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="12%">Tanggal</th>
                <th width="15%">Pembimbing</th>
                <th width="20%">Siswa</th>
                <th width="20%">Perusahaan</th>
                <th width="22%">Catatan</th>
                <th width="8%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kunjungans as $index => $k)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    {{ $k->tanggal->format('d/m/Y') }}<br>
                    <small>{{ $k->waktu }}</small>
                </td>
                <td>{{ $k->pembimbing->name ?? '-' }}</td>
                <td>
                    @if($k->siswa)
                        {{ $k->siswa->name }}
                    @else
                        @php
                            $siswaBinaanDiPerusahaan = $k->perusahaan 
                                ? $k->perusahaan->siswaProfiles->where('pembimbing_id', $k->pembimbing_id) 
                                : collect();
                        @endphp
                        @forelse($siswaBinaanDiPerusahaan as $sp)
                            &bull; {{ $sp->user->name ?? '-' }}<br>
                        @empty
                            -
                        @endforelse
                    @endif
                </td>
                <td>
                    {{ $k->perusahaan->nama ?? '-' }}<br>
                    <small>{{ \Str::limit($k->perusahaan->alamat ?? '', 50) }}</small>
                </td>
                <td>
                    @if($k->status === 'rencana')
                        <strong>Rencana:</strong> {{ $k->catatan_rencana }}
                    @else
                        <strong>Hasil:</strong> {{ $k->catatan }}
                    @endif
                </td>
                <td class="text-center">
                    @if($k->status === 'rencana')
                        <span class="badge-rencana">Rencana</span>
                    @else
                        <span class="badge-selesai">Selesai</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Belum ada data kunjungan</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
        <br><br><br>
        <p><strong>Admin Penanggung Jawab</strong></p>
    </div>

</body>
</html>
