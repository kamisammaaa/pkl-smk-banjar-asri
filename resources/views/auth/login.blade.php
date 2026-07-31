<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Header Form -->
        <div class="mb-6 text-center">
            <h2 class="text-xl font-bold text-white drop-shadow-[0_0_10px_rgba(255,255,255,0.3)]">🔐 Masuk ke Akun</h2>
            <p class="text-sm text-crypto-textMuted mt-1">Selamat datang kembali di Sistem PKL</p>
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
                    <a class="underline text-sm text-gray-400 hover:text-crypto-accent rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-crypto-accent focus:ring-offset-crypto-dark transition-colors" href="{{ route('password.request') }}">
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
                <input id="remember_me" type="checkbox" class="rounded border-white/20 bg-crypto-dark text-crypto-accent shadow-sm focus:ring-crypto-accent focus:ring-offset-crypto-dark" name="remember">
                <span class="ms-2 text-sm text-gray-400">{{ __('Ingat saya') }}</span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end mt-6">
            <x-primary-button class="ms-3 w-full justify-center">
                {{ __('🚀 Masuk') }}
            </x-primary-button>
        </div>

        <!-- Link ke Register -->
        <div class="text-center text-sm text-gray-400 mt-6 pt-4 border-t border-white/10">
            Belum punya akun? 
            <a href="{{ route('register') }}" class="text-crypto-success hover:text-crypto-successHover font-bold hover:underline transition-colors drop-shadow-[0_0_8px_rgba(14,203,129,0.3)]">
                Daftar sebagai Siswa
            </a>
        </div>
    </form>
</x-guest-layout>