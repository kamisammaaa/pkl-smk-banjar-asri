{{-- 
    Komponen Upload Foto/Dokumen dengan Progress Bar
    
    Props:
      - $name       : nama input (e.g. 'foto', 'bukti')
      - $id         : id elemen (opsional, default = "upload_{$name}")
      - $label      : teks label (e.g. 'Foto Selfie Lokasi PKL')
      - $accept     : accept attribute (e.g. 'image/*', 'image/*,.pdf')
      - $required   : boolean (default false)
      - $maxMb      : ukuran maks dalam MB (default 20)
      - $hint       : teks hint di bawah (opsional)
      - $btnColor   : warna tombol (default 'blue') - options: blue, green, purple, orange
      - $disabled   : boolean, nonaktifkan input (default false)
      - $existingUrl: URL file yang sudah ada (opsional, untuk edit)
      - $existingLabel: label untuk file yang ada (opsional)
--}}

@php
    $inputId   = $id ?? 'upload_' . $name;
    $maxBytes  = ($maxMb ?? 20) * 1024 * 1024;
    $maxMbVal  = $maxMb ?? 20;
    $serverMaxMb = round((int) ini_get('upload_max_filesize') / 1, 0);
    $effectiveMax = min($maxMbVal, (int) ini_get('upload_max_filesize'));
    
    $colors = [
        'blue'    => ['btn' => 'file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100', 'bar' => 'bg-blue-500', 'ring' => 'focus:ring-blue-200'],
        'green'   => ['btn' => 'file:bg-green-50 file:text-green-700 hover:file:bg-green-100', 'bar' => 'bg-green-500', 'ring' => 'focus:ring-green-200'],
        'emerald' => ['btn' => 'file:bg-emerald-500/20 file:text-emerald-400 hover:file:bg-emerald-500/30 file:border file:border-emerald-500/30 shadow-inner', 'bar' => 'bg-crypto-success', 'ring' => 'focus:ring-crypto-success'],
        'purple'  => ['btn' => 'file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100', 'bar' => 'bg-purple-500', 'ring' => 'focus:ring-purple-200'],
        'orange'  => ['btn' => 'file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100', 'bar' => 'bg-orange-500', 'ring' => 'focus:ring-orange-200'],
    ];
    $color = $colors[$btnColor ?? 'blue'] ?? $colors['blue'];
@endphp

<div class="upload-wrapper" data-upload-id="{{ $inputId }}">

    {{-- Preview / File yang sudah ada --}}
    @if(!empty($existingUrl))
    <div class="mb-2 flex items-center gap-3 bg-gray-50 border border-gray-200 p-2.5 rounded-xl" id="{{ $inputId }}_existing">
        @php
            $ext = strtolower(pathinfo($existingUrl, PATHINFO_EXTENSION));
            $isImg = in_array($ext, ['jpg','jpeg','png','webp','gif']);
        @endphp
        @if($isImg)
            <img src="{{ $existingUrl }}" alt="File saat ini" class="w-16 h-16 object-cover rounded-lg border border-gray-200 flex-shrink-0">
        @else
            <div class="w-16 h-16 flex items-center justify-center rounded-lg border border-gray-200 bg-red-50 flex-shrink-0">
                <span class="text-2xl">📄</span>
            </div>
        @endif
        <div class="flex-1 min-w-0">
            <p class="text-xs text-gray-500">{{ $existingLabel ?? 'File saat ini sudah tersimpan.' }}</p>
            <a href="{{ $existingUrl }}" target="_blank" class="text-xs text-blue-600 hover:underline">🔍 Lihat file</a>
        </div>
    </div>
    @endif

    {{-- Input File --}}
    <input 
        type="file" 
        name="{{ $name }}" 
        id="{{ $inputId }}"
        accept="{{ $accept ?? 'image/*' }}"
        {{ ($required ?? false) ? 'required' : '' }}
        {{ ($disabled ?? false) ? 'disabled' : '' }}
        data-max-bytes="{{ $maxBytes }}"
        data-max-mb="{{ $effectiveMax }}"
        class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm 
               file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold
               {{ $color['btn'] }} {{ $color['ring'] }}
               focus:ring-2 focus:border-transparent transition cursor-pointer
               disabled:opacity-50 disabled:cursor-not-allowed"
        onchange="handleFileSelect('{{ $inputId }}')"
    >

    {{-- Hint --}}
    <p class="text-xs text-gray-500 mt-1" id="{{ $inputId }}_hint">
        {{ $hint ?? "📎 Format: JPG, PNG. Maks. {$effectiveMax}MB (akan dikompres otomatis oleh server)." }}
    </p>

    {{-- Error Validasi --}}
    @error($name)
        <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
            <span>⚠️</span> {{ $message }}
        </p>
    @enderror

    {{-- ====== Progress Upload Area ====== --}}
    <div id="{{ $inputId }}_progress_area" class="hidden mt-3">
        
        {{-- Preview gambar yang dipilih --}}
        <div id="{{ $inputId }}_preview_wrap" class="hidden mb-2">
            <div class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-xl p-2.5">
                <div id="{{ $inputId }}_thumb_wrap" class="flex-shrink-0">
                    <img id="{{ $inputId }}_thumb" src="" alt="Preview" class="w-14 h-14 object-cover rounded-lg border border-slate-200">
                </div>
                <div class="flex-1 min-w-0">
                    <p id="{{ $inputId }}_file_name" class="text-xs font-semibold text-slate-700 truncate"></p>
                    <p id="{{ $inputId }}_file_size" class="text-xs text-slate-500"></p>
                    <p class="text-xs text-emerald-600 mt-0.5">✅ Foto akan dikompres otomatis</p>
                </div>
                <button type="button" onclick="clearUpload('{{ $inputId }}')" 
                        class="flex-shrink-0 text-slate-400 hover:text-red-500 transition text-lg leading-none" 
                        title="Hapus pilihan">✕</button>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div id="{{ $inputId }}_bar_wrap" class="hidden">
            <div class="flex items-center justify-between text-xs text-slate-600 mb-1">
                <span id="{{ $inputId }}_bar_label">📤 Mengupload...</span>
                <span id="{{ $inputId }}_bar_pct">0%</span>
            </div>
            <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
                <div id="{{ $inputId }}_bar" class="h-full {{ $color['bar'] }} rounded-full transition-all duration-300" style="width:0%"></div>
            </div>
            <p id="{{ $inputId }}_bar_status" class="text-xs text-slate-500 mt-1"></p>
        </div>

        {{-- Error ukuran file --}}
        <div id="{{ $inputId }}_size_error" class="hidden bg-red-50 border border-red-200 rounded-xl p-3 text-sm text-red-700 flex items-start gap-2">
            <span class="text-lg flex-shrink-0">🚫</span>
            <div>
                <p class="font-semibold">File terlalu besar!</p>
                <p id="{{ $inputId }}_size_error_msg" class="text-xs mt-0.5"></p>
                <p class="text-xs mt-1 text-red-600">Silakan kompres foto terlebih dahulu atau pilih foto dengan ukuran yang lebih kecil.</p>
            </div>
        </div>

    </div>

</div>
