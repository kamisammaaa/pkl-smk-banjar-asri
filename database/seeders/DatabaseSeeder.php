<?php

namespace Database\Seeders;
use App\Models\User;
use App\Models\Jurusan;
use App\Models\Perusahaan;
use App\Models\SiswaProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Jurusan
        $jurusan = [
            Jurusan::create(['nama' => 'TKJ']),
            Jurusan::create(['nama' => 'TKR']),
            Jurusan::create(['nama' => 'TAV']),
        ];

        // 2. Pembimbing
        $pembimbing1 = User::create(['name'=>'Pak Andi (TKJ)','email'=>'pembimbing.tkj@smk.id','password'=>Hash::make('123456'),'role'=>'pembimbing']);
        $pembimbing2 = User::create(['name'=>'Bu Siti (TKR)','email'=>'pembimbing.tkr@smk.id','password'=>Hash::make('123456'),'role'=>'pembimbing']);

        // 3. Perusahaan
        $perusahaan1 = Perusahaan::create(['nama'=>'PT. Teknologi Nusantara','alamat'=>'Jl. Industri No. 10','kontak'=>'08123456789','pembimbing_id'=>$pembimbing1->id]);
        $perusahaan2 = Perusahaan::create(['nama'=>'Bengkel Maju Jaya','alamat'=>'Jl. Raya Otomotif No. 5','kontak'=>'08234567890','pembimbing_id'=>$pembimbing2->id]);

        // 4. Siswa
        $siswa1 = User::create(['name'=>'Rizky (TKJ)','email'=>'rizky@smk.id','password'=>Hash::make('123456'),'role'=>'siswa']);
        $siswa2 = User::create(['name'=>'Dewi (TKR)','email'=>'dewi@smk.id','password'=>Hash::make('123456'),'role'=>'siswa']);

        // 5. Relasi Siswa Profile
        SiswaProfile::create(['user_id'=>$siswa1->id, 'nis'=>'2024001', 'jurusan_id'=>$jurusan[0]->id, 'perusahaan_id'=>$perusahaan1->id, 'pembimbing_id'=>$pembimbing1->id]);
        SiswaProfile::create(['user_id'=>$siswa2->id, 'nis'=>'2024002', 'jurusan_id'=>$jurusan[1]->id, 'perusahaan_id'=>$perusahaan2->id, 'pembimbing_id'=>$pembimbing2->id]);
    }
}