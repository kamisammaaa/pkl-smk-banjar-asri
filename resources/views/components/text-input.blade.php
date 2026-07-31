@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-[#0a0f1d]/50 border-white/10 text-white placeholder-gray-500 focus:border-crypto-accent focus:ring-crypto-accent rounded-xl shadow-sm backdrop-blur-sm']) }}>
