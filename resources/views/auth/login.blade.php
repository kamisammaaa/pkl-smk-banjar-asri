<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Header Form -->
        <div class="mb-6 text-center">
            <h2 class="text-xl font-bold text-gray-800">🔐 Masuk ke Akun</h2>
            <p class="text-sm text-gray-500 mt-1">Selamat datang kembali di Sistem PKL</p>
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="siswa@smk.id" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" />
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                        {{ __('Lupa password?') }}
                    </a>
                @endif
            </div>

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="••••••••" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Ingat saya') }}</span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end mt-6">
            <x-primary-button class="ms-3 w-full justify-center">
                {{ __('🚀 Masuk') }}
            </x-primary-button>
        </div>

        <!-- Link ke Register -->
        <div class="text-center text-sm text-gray-600 mt-6 pt-4 border-t border-gray-100">
            Belum punya akun? 
            <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-800 font-medium hover:underline transition">
                Daftar sebagai Siswa
            </a>
        </div>
    </form>
</x-guest-layout>