<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-3 bg-crypto-accent border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-crypto-accentHover focus:bg-crypto-accentHover active:bg-crypto-accentHover focus:outline-none focus:ring-2 focus:ring-crypto-accent focus:ring-offset-2 focus:ring-offset-crypto-dark transition-all duration-300 shadow-[0_0_20px_rgba(112,0,255,0.3)] hover:shadow-[0_0_30px_rgba(112,0,255,0.5)] active:scale-95']) }}>
    {{ $slot }}
</button>
