<!-- ================================================================ -->
<!-- 1. HERO & BANNER RUMAH MAKAN (DESKTOP: GRID 2 KOLOM / HP: STACK) -->
<!-- ================================================================ -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-center bg-white p-6 sm:p-8 rounded-3xl border border-[#EAE3D9] shadow-2xs">
    
    <!-- Kolom Teks Pengantar -->
    <div class="lg:col-span-6 space-y-4 text-center lg:text-left">
        <!-- Tag Emas Selamat Datang -->
        <div class="font-bold text-[11px] sm:text-xs tracking-[0.25em] text-[#C59B27] uppercase select-none">
            &#10022; SELAMAT DATANG &#10022;
        </div>

        <!-- Badges: 100% Halal & Sejak 2009 -->
        <div class="flex items-center justify-center lg:justify-start gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#F8EBEB] text-[#7B1113] border border-[#F0D5D5] shadow-2xs">
                <svg class="w-3.5 h-3.5 text-[#7B1113]" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                100% Halal
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#FAF0D9] text-[#8C6414] border border-[#F3E2B8] shadow-2xs">
                <svg class="w-3.5 h-3.5 text-[#8C6414]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Sejak 2009
            </span>
        </div>

        <!-- Judul Utama Serif -->
        <h1 class="font-brand-serif font-black text-2xl sm:text-3xl lg:text-4xl text-[#7B1113] tracking-wide leading-tight">
            Citarasa Minang<br class="hidden sm:inline"> Autentik & Legendaris
        </h1>

        <!-- Deskripsi Pengantar -->
        <p class="text-xs sm:text-sm text-stone-600 leading-relaxed max-w-xl font-brand-sub">
            Menghadirkan kelezatan rempah asli Minangkabau turun-temurun dari ranah Bukittinggi, dimasak perlahan dengan kehangatan cinta tradisi langsung ke meja Anda.
        </p>
    </div>

    <!-- Kolom Foto Banner Rumah Makan -->
    <div class="lg:col-span-6">
        @php
            $fotoWarungPublic = 'images/beranda/warung.jpg';
            $adaFotoWarung = file_exists(public_path($fotoWarungPublic));
        @endphp

        <div class="rounded-2xl sm:rounded-3xl overflow-hidden shadow-sm border border-[#EAE3D9] bg-[#F5EFE6] relative">
            @if ($adaFotoWarung)
                <img src="{{ asset($fotoWarungPublic) }}" alt="Rumah Makan Tiga Putri" class="w-full h-56 sm:h-72 lg:h-80 object-cover" />
            @else
                {{-- Placeholder polos kotak krem dengan ikon (tanpa link internet) --}}
                <div class="w-full h-56 sm:h-72 lg:h-80 flex flex-col items-center justify-center p-6 text-center text-stone-500 bg-gradient-to-b from-[#F7F2EB] to-[#EFE7DC]">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-[#EAE1D3] flex items-center justify-center mb-2 shadow-inner">
                        <svg class="w-7 h-7 sm:w-8 sm:h-8 text-[#7B1113]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <span class="text-xs sm:text-sm font-bold text-stone-700">Foto Rumah Makan Tiga Putri</span>
                    <span class="text-[10px] sm:text-[11px] text-stone-500 mt-1">Letakkan file asli di: <code class="font-mono bg-[#E8DFCFA] px-1.5 py-0.5 rounded text-[10px]">public/images/beranda/warung.jpg</code></span>
                </div>
            @endif

            <!-- Overlay Badges di Bawah Foto Sesuai Prototype -->
            <div class="absolute bottom-3 inset-x-3 flex items-center justify-between pointer-events-none">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-stone-900/80 text-white text-[11px] font-medium backdrop-blur-xs shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-[#C59B27]"></span>
                    Rumah Makan Tiga Putri
                </span>
                <span class="px-3.5 py-1.5 rounded-full bg-[#7B1113] text-white text-[11px] font-bold shadow-xs">
                    40+ Menu Tersedia
                </span>
            </div>
        </div>
    </div>

</div>

<!-- ================================================================ -->
<!-- 2. PESAN ONLINE LANGSUNG                                         -->
<!-- ================================================================ -->
<div class="space-y-3">
    <div class="flex items-center gap-2">
        <svg class="w-4 h-4 text-[#7B1113]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
        <h2 class="font-bold text-sm sm:text-base text-stone-800">
            Pesan Online Langsung
        </h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
        <!-- GoFood -->
        <a href="https://gofood.co.id" target="_blank" rel="noopener noreferrer" class="bg-white hover:bg-[#FAF7F2] p-4 rounded-2xl border border-[#EAE3D9] flex items-center sm:flex-col sm:justify-center text-left sm:text-center gap-3 sm:gap-2 transition shadow-2xs group">
            <div class="w-11 h-11 rounded-full bg-red-50 text-red-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <div>
                <span class="text-sm font-bold text-stone-800 block">GoFood</span>
                <span class="text-[11px] text-stone-500 sm:hidden">Pesan antar cepat via Gojek</span>
            </div>
        </a>

        <!-- GrabFood -->
        <a href="https://food.grab.com" target="_blank" rel="noopener noreferrer" class="bg-white hover:bg-[#FAF7F2] p-4 rounded-2xl border border-[#EAE3D9] flex items-center sm:flex-col sm:justify-center text-left sm:text-center gap-3 sm:gap-2 transition shadow-2xs group">
            <div class="w-11 h-11 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <span class="text-sm font-bold text-stone-800 block">GrabFood</span>
                <span class="text-[11px] text-stone-500 sm:hidden">Pesan antar lezat via Grab</span>
            </div>
        </a>

        <!-- ShopeeFood -->
        <a href="https://shopee.co.id/shopeefood" target="_blank" rel="noopener noreferrer" class="bg-white hover:bg-[#FAF7F2] p-4 rounded-2xl border border-[#EAE3D9] flex items-center sm:flex-col sm:justify-center text-left sm:text-center gap-3 sm:gap-2 transition shadow-2xs group">
            <div class="w-11 h-11 rounded-full bg-amber-50 text-orange-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <span class="text-sm font-bold text-stone-800 block">ShopeeFood</span>
                <span class="text-[11px] text-stone-500 sm:hidden">Promo & diskon ShopeeFood</span>
            </div>
        </a>
    </div>
</div>

<!-- ================================================================ -->
<!-- 3. MENU FAVORIT                                                  -->
<!-- ================================================================ -->
@php
    // Ambil menu aktif dari database; data fallback hanya dipakai jika tabel menu kosong
    $menusFromDb = \App\Models\Menu::where('status_stok', 'tersedia')
        ->orderBy('id_menu')
        ->take(5)
        ->get();

    if ($menusFromDb->isEmpty()) {
        $menuFavoritList = collect([
            (object)[
                'nama_menu' => 'Rendang Daging',
                'harga' => 24000,
                'foto_menu' => null,
                'badge' => 'Terfavorit',
                'is_special' => false,
            ],
            (object)[
                'nama_menu' => 'Ayam Pop Minang',
                'harga' => 22000,
                'foto_menu' => null,
                'badge' => null,
                'is_special' => false,
            ],
            (object)[
                'nama_menu' => 'Gulai Tunjang',
                'harga' => 26000,
                'foto_menu' => null,
                'badge' => null,
                'is_special' => false,
            ],
            (object)[
                'nama_menu' => 'Ayam Bakar Minang',
                'harga' => 22000,
                'foto_menu' => null,
                'badge' => null,
                'is_special' => false,
            ],
            (object)[
                'nama_menu' => 'Gulai Kepala Kakap Merah',
                'harga' => 45000,
                'foto_menu' => null,
                'badge' => 'PORSI ISTIMEWA',
                'is_special' => true,
            ],
        ]);
    } else {
        $menuFavoritList = $menusFromDb->map(function ($item, $index) use ($menusFromDb) {
            $isSpecial = ($index === 4 || ($index === $menusFromDb->count() - 1 && $menusFromDb->count() >= 5));
            $badge = null;
            if ($index === 0) {
                $badge = 'Terfavorit';
            } elseif ($isSpecial) {
                $badge = 'PORSI ISTIMEWA';
            }
            return (object)[
                'nama_menu' => $item->nama_menu,
                'harga' => $item->harga,
                'foto_menu' => $item->foto_menu,
                'badge' => $badge,
                'is_special' => $isSpecial,
            ];
        });
    }
@endphp

<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="font-brand-serif font-black text-xl sm:text-2xl text-[#7B1113] tracking-wide">
            Menu Favorit
        </h2>
        <a href="{{ route('menu.index') }}" class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 bg-[#7B1113] hover:bg-[#600D0F] text-white font-bold text-xs rounded-xl shadow-xs transition">
            <span>Lihat Semua Menu</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
        </a>
    </div>

    <!-- Grid Menu Responsif: 2 Kolom di HP, 3 Kolom di Tablet, 5 Kolom Seimbang di Layar Lebar -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
        @foreach ($menuFavoritList as $item)
            <div class="bg-white rounded-2xl border border-[#EAE3D9] overflow-hidden shadow-2xs flex flex-col justify-between relative group hover:shadow-md transition duration-200">
                <!-- Foto / Placeholder Menu -->
                <div class="h-32 sm:h-36 bg-[#F5EFE6] relative overflow-hidden flex items-center justify-center">
                    @if ($item->foto_menu && \Illuminate\Support\Facades\Storage::disk('public')->exists($item->foto_menu))
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($item->foto_menu) }}" alt="{{ $item->nama_menu }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" />
                    @else
                        {{-- Placeholder polos kotak krem dengan icon makanan --}}
                        <div class="w-full h-full flex flex-col items-center justify-center text-stone-400 bg-gradient-to-b from-[#FAF7F2] to-[#EFE7DC]">
                            <svg class="w-8 h-8 text-[#C59B27]/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span class="text-[9px] text-stone-400 mt-1">Menu Tiga Putri</span>
                        </div>
                    @endif

                    @if (!empty($item->badge))
                        <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md text-white text-[9.5px] font-bold tracking-wide shadow-xs {{ $item->badge === 'PORSI ISTIMEWA' ? 'bg-[#C59B27]' : 'bg-[#7B1113]' }}">
                            {{ $item->badge }}
                        </span>
                    @endif
                </div>

                <!-- Info Nama & Harga Menu -->
                <div class="p-3.5 flex-1 flex flex-col justify-between">
                    <h3 class="font-bold text-xs sm:text-sm text-stone-900 leading-snug line-clamp-2">
                        {{ $item->nama_menu }}
                    </h3>
                    <p class="text-xs sm:text-sm font-black text-[#7B1113] mt-2 font-mono">
                        Rp {{ number_format($item->harga, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Tombol Lihat Semua Menu (Khusus Tampilan Mobile) -->
    <div class="sm:hidden pt-1">
        <a href="{{ route('menu.index') }}" class="w-full py-2.5 bg-[#7B1113] hover:bg-[#600D0F] active:bg-[#4E090B] text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-2">
            <svg class="w-4 h-4 text-[#FAF7F2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
            </svg>
            <span>Lihat Semua Menu</span>
        </a>
    </div>
</div>
