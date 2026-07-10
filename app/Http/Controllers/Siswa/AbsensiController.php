<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    /**
     * Display absensi history for authenticated student.
     * TIDAK memanggil generateAlpha di sini agar tidak ada side-effect pada listing.
     */
    public function index()
    {
        $absensis = Absensi::where('siswa_user_id', auth()->id())
            ->latest('tanggal')
            ->paginate(10);

        // Informasi periode PKL untuk ditampilkan di view
        $user      = Auth::user();
        $profile   = $user->siswaProfile;
        $perusahaan = $profile?->perusahaan;
        $periode   = $perusahaan?->periodePKL;

        return view('siswa.absensi.index', compact('absensis', 'periode'));
    }

    /**
     * Show form to create new absensi.
     */
    public function create()
    {
        $user = Auth::user();

        // 🛡️ Cek Profil dan Perusahaan
        $profile = $user->siswaProfile;
        if (!$profile || !$profile->perusahaan_id) {
            return redirect()->route('siswa.absensi.index')
                ->with('error', '⚠️ Anda belum ditempatkan di industri/perusahaan mitra.');
        }

        $perusahaan = $profile->perusahaan;
        if (!$perusahaan || !$perusahaan->periode_pkl_id) {
            return redirect()->route('siswa.absensi.index')
                ->with('error', '⚠️ Industri Anda belum dikaitkan dengan Periode PKL.');
        }

        // 🛡️ Cek Periode PKL Siswa
        $periode = $perusahaan->periodePKL;
        $today   = now()->startOfDay();

        if (!$periode || !$periode->is_active) {
            return redirect()->route('siswa.absensi.index')
                ->with('error', '⚠️ Periode PKL Anda belum aktif atau tidak ditemukan.');
        }

        $startDate = \Carbon\Carbon::parse($periode->tanggal_mulai)->startOfDay();
        $endDate   = \Carbon\Carbon::parse($periode->tanggal_selesai)->endOfDay();

        if ($today->lt($startDate)) {
            $mulaiStr = $startDate->format('d M Y');
            return redirect()->route('siswa.absensi.index')
                ->with('error', "⚠️ Periode PKL Anda belum dimulai. Absensi baru bisa dilakukan mulai <strong>{$mulaiStr}</strong>.");
        }

        if ($today->gt($endDate)) {
            $selesaiStr = $endDate->format('d M Y');
            return redirect()->route('siswa.absensi.index')
                ->with('error', "⚠️ Periode PKL Anda sudah selesai pada <strong>{$selesaiStr}</strong>. Tidak bisa melakukan absensi lagi.");
        }

        // 🛡️ Cek apakah sudah absen hari ini
        $existing = Absensi::where('siswa_user_id', $user->id)
                           ->whereDate('tanggal', $today)
                           ->first();

        if ($existing) {
            return redirect()->route('siswa.absensi.index')
                ->with('error', '⚠️ Anda sudah melakukan absensi hari ini! Status: <strong>' . $existing->getStatusLabel() . '</strong>');
        }

        return view('siswa.absensi.create', compact('periode', 'perusahaan'));
    }

    /**
     * Store new absensi.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // 🛡️ Cek Profil dan Perusahaan
        $profile = $user->siswaProfile;
        if (!$profile || !$profile->perusahaan_id) {
            return back()->with('error', '⚠️ Anda belum ditempatkan di industri/perusahaan mitra.');
        }

        $perusahaan = $profile->perusahaan;
        if (!$perusahaan || !$perusahaan->periode_pkl_id) {
            return back()->with('error', '⚠️ Industri Anda belum dikaitkan dengan Periode PKL.');
        }

        // 🛡️ Validasi Periode PKL
        $periode = $perusahaan->periodePKL;
        $today   = now()->startOfDay();

        if (!$periode || !$periode->is_active) {
            return back()->with('error', '⚠️ Periode PKL Anda belum aktif atau tidak ditemukan.');
        }

        $startDate = \Carbon\Carbon::parse($periode->tanggal_mulai)->startOfDay();
        $endDate   = \Carbon\Carbon::parse($periode->tanggal_selesai)->endOfDay();

        if ($today->lt($startDate)) {
            $mulaiStr = $startDate->format('d M Y');
            return back()->with('error', "⚠️ Periode PKL Anda belum dimulai. Absensi baru bisa dilakukan mulai <strong>{$mulaiStr}</strong>.");
        }

        if ($today->gt($endDate)) {
            $selesaiStr = $endDate->format('d M Y');
            return back()->with('error', "⚠️ Periode PKL Anda sudah selesai pada <strong>{$selesaiStr}</strong>.");
        }

        // 🛡️ Cek sudah absen hari ini
        $existing = Absensi::where('siswa_user_id', $user->id)
                           ->whereDate('tanggal', $today)
                           ->first();

        if ($existing) {
            return back()->with('error', '⚠️ Anda sudah melakukan absensi hari ini!');
        }

        // 📝 Validasi Dinamis Berdasarkan Status
        $request->validate([
            'status' => 'required|in:hadir,sakit,izin,libur',
            'alasan' => 'required_if:status,sakit,izin,libur|max:500',
            'bukti'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:20480',
            'foto'   => 'required_if:status,hadir|image|max:20480',
        ], [
            'status.required'        => 'Pilih status absensi terlebih dahulu.',
            'status.in'              => 'Status absensi tidak valid.',
            'alasan.required_if'     => 'Wajib mengisi alasan jika memilih Sakit, Izin, atau Libur.',
            'foto.required_if'       => 'Wajib upload foto selfie jika memilih Hadir.',
        ]);

        // 🗃️ Persiapan Data
        $data = [
            'siswa_user_id' => $user->id,
            'tanggal'       => $today->format('Y-m-d'),
            'status'        => $request->status,
            'is_verified'   => false, // Wajib verifikasi pembimbing
            'ip_address'    => $request->ip(),
        ];

        // 🔄 Logic Berdasarkan Status
        if ($request->status === 'hadir') {
            $data['check_in']    = now()->format('H:i:s');
            $data['lokasi_nama'] = 'Kehadiran (IP: ' . $request->ip() . ')';

            if ($request->hasFile('foto')) {
                $data['foto'] = \App\Helpers\UploadHelper::uploadAndCompress($request->file('foto'), 'absensi/foto/' . date('Y/m'), 'public');
            }
        } else {
            // Sakit / Izin / Libur
            $data['alasan']   = $request->alasan;
            $data['check_in'] = null;

            // Upload bukti (opsional untuk libur, wajib untuk sakit/izin di validasi)
            if ($request->hasFile('bukti')) {
                $data['bukti_file'] = \App\Helpers\UploadHelper::uploadAndCompress($request->file('bukti'), 'absensi/bukti/' . date('Y/m'), 'public');
            }
        }

        // 💾 Simpan ke Database
        $absensi = Absensi::create($data);

        // 🕐 Hitung keterlambatan setelah record dibuat (khusus hadir)
        if ($request->status === 'hadir' && $perusahaan) {
            $checkInCarbon = \Carbon\Carbon::parse($data['check_in']);
            $isLate        = $perusahaan->isLate($checkInCarbon);
            $meniLate      = $isLate ? $perusahaan->meniTerlambat($checkInCarbon) : 0;

            $absensi->update([
                'is_late'         => $isLate,
                'terlambat_menit' => $isLate ? $meniLate : null,
            ]);
        }

        $statusLabel = match($request->status) {
            'hadir' => 'Hadir',
            'sakit' => 'Sakit',
            'izin'  => 'Izin',
            'libur' => 'Libur',
            default => ucfirst($request->status),
        };

        return redirect()
            ->route('siswa.absensi.index')
            ->with('success', "✅ Absensi <strong>{$statusLabel}</strong> berhasil dikirim! Menunggu verifikasi pembimbing.");
    }
}