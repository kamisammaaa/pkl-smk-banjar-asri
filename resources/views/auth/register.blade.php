<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <!-- Header Form -->
        <div class="mb-6 text-center">
            <h2 class="text-xl font-bold text-gray-800">🎓 Daftar Akun Siswa</h2>
            <p class="text-sm text-gray-500 mt-1">Isi data berikut untuk mendaftar PKL</p>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border-l-4 border-green-500 text-green-800 rounded text-sm">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-800 rounded text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- NIS -->
        <div>
            <x-input-label for="nis" :value="__('NIS (Nomor Induk Siswa)')" />
            <x-text-input id="nis" class="block mt-1 w-full" type="text" name="nis" :value="old('nis')" required autofocus autocomplete="off" placeholder="Contoh: 2024001234" maxlength="20" />
            <x-input-error :messages="$errors->get('nis')" class="mt-2" />
            <p class="text-xs text-gray-500 mt-1">Gunakan NIS resmi dari sekolah.</p>
        </div>

        <!-- Nama Lengkap -->
        <div class="mt-4">
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autocomplete="name" placeholder="Sesuai data sekolah" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email Aktif')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="email" placeholder="siswa@contoh.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Jurusan & Kelas (Row) -->
        <div class="mt-4 grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="jurusan_id" :value="__('Jurusan')" />
                <select id="jurusan_id" name="jurusan_id" required class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm bg-white">
                    <option value="">Pilih Jurusan</option>
                    @foreach($jurusanList as $j)
                        <option value="{{ $j->id }}" {{ old('jurusan_id') == $j->id ? 'selected' : '' }}>
                            {{ $j->nama }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('jurusan_id')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="kelas" :value="__('Kelas')" />
                <select id="kelas" name="kelas" required class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm bg-white">
                    <option value="">Pilih Kelas</option>
                    @foreach(['X', 'XI', 'XII'] as $tingkat)
                        @foreach(['TKJ', 'TAV', 'TKR'] as $k)
                            <option value="{{ $tingkat.$k }}" {{ old('kelas') == $tingkat.$k ? 'selected' : '' }}>
                                {{ $tingkat }} {{ $k }}
                            </option>
                        @endforeach
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('kelas')" class="mt-2" />
            </div>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" minlength="6" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter.</p>
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end mt-6">
            <x-primary-button class="ms-4">
                {{ __('🚀 Daftar Sekarang') }}
            </x-primary-button>
        </div>

        <!-- Link Login -->
        <div class="text-center text-sm text-gray-600 mt-6 pt-4 border-t border-gray-100">
            Sudah punya akun? 
            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-medium hover:underline transition">
                Login di sini
            </a>
        </div>
    </form>

    <!-- Info Box -->
    <div class="mt-6 p-3 bg-blue-50 rounded-lg border border-blue-100 text-center">
        <p class="text-xs text-blue-700">
            ⚠️ Akun akan aktif setelah disetujui administrator.<br>
            Butuh bantuan? <span class="font-medium">admin@smkbanjarasri.sch.id</span>
        </p>
    </div>
</x-guest-layout>
