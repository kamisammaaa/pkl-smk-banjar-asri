<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Absensi Siswa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
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
            padding: 5px;
            text-align: left;
            vertical-align: middle;
        }
        .table th {
            background-color: #f4f4f4;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .bg-green { background-color: #e6f4ea; color: #1e8e3e; }
        .bg-yellow { background-color: #fef7e0; color: #f29900; }
        .bg-orange { background-color: #fce8e6; color: #d93025; }
        .bg-blue { background-color: #e8f0fe; color: #1a73e8; }
        .bg-purple { background-color: #f3e8fd; color: #9334e6; }
        .bg-red { background-color: #fce8e6; color: #c5221f; font-weight: bold; }
        
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>REKAP ABSENSI SISWA PKL</h1>
        <p>SMK BANJAR ASRI</p>
        <p>Bulan: {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}</p>
    </div>

    @if(request('pembimbing_id') || request('jurusan_id') || request('filter_masalah'))
    <div style="margin-bottom: 15px;">
        <strong>Filter Aktif:</strong><br>
        @if(request('pembimbing_id')) - Pembimbing: {{ \App\Models\User::find(request('pembimbing_id'))->name ?? '-' }}<br> @endif
        @if(request('jurusan_id')) - Jurusan: {{ \App\Models\Jurusan::find(request('jurusan_id'))->nama ?? '-' }}<br> @endif
        @if(request('filter_masalah')) - Masalah Kehadiran: {{ ucfirst(request('filter_masalah')) }}<br> @endif
    </div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="15%">Nama Siswa</th>
                <th width="12%">Jurusan</th>
                <th width="12%">Pembimbing</th>
                <th width="7%">Hadir</th>
                <th width="7%">Telat</th>
                <th width="6%">Sakit</th>
                <th width="6%">Izin</th>
                <th width="6%">Libur</th>
                <th width="6%">Alpha</th>
                <th width="8%">Hari Aktif</th>
                <th width="8%">% Hadir</th>
                <th width="10%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekap as $index => $r)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $r['siswa']->name }}</strong><br>
                    <small>{{ $r['siswa']->siswaProfile?->nis ?? '-' }}</small>
                </td>
                <td>{{ $r['siswa']->siswaProfile?->jurusan?->nama ?? '-' }}</td>
                <td>{{ $r['siswa']->siswaProfile?->pembimbing?->name ?? '-' }}</td>
                
                <td class="text-center bg-green"><strong>{{ $r['hadir'] }}</strong></td>
                <td class="text-center bg-yellow"><strong>{{ $r['terlambat'] }}</strong></td>
                <td class="text-center bg-orange">{{ $r['sakit'] }}</td>
                <td class="text-center bg-blue">{{ $r['izin'] }}</td>
                <td class="text-center bg-purple">{{ $r['libur'] }}</td>
                <td class="text-center bg-red">{{ $r['alpha'] }}</td>
                
                <td class="text-center font-bold">{{ $r['hari_aktif'] }}</td>
                <td class="text-center font-bold">{{ $r['persentase'] }}%</td>
                <td class="text-center">
                    @php
                        $pct = $r['persentase'];
                        $ket = match(true) {
                            $pct >= 90 => 'Sangat Baik',
                            $pct >= 75 => 'Baik',
                            $pct >= 50 => 'Cukup',
                            $pct > 0   => 'Kurang',
                            default    => '-',
                        };
                    @endphp
                    {{ $ket }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="13" class="text-center">Belum ada data rekap absensi yang sesuai filter.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="margin-top: 20px; font-size: 10px; color: #555;">
        <strong>Keterangan:</strong>
        <ul style="margin-top: 5px; padding-left: 15px;">
            <li><strong>Hari Aktif</strong> = Total hari absensi dikurangi hari Libur</li>
            <li><strong>% Hadir</strong> = (Hadir + Terlambat ÷ Hari Aktif) × 100</li>
        </ul>
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
        <br><br><br>
        <p><strong>Admin Penanggung Jawab</strong></p>
    </div>

</body>
</html>
