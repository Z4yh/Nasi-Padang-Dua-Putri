@php
    // Deteksi role aktif (mendukung opsi preview via query string ?preview_role=guest|penjual|super_admin untuk kemudahan pengujian UI)
    $activeRole = request('preview_role') ?? (Auth::check() ? Auth::user()->role : 'guest');
    $isGuest = ($activeRole === 'guest');
    $isSuperAdmin = ($activeRole === 'super_admin');
    $isAdminPenjual = ($activeRole === 'penjual' || $activeRole === 'admin');

    // Deteksi status rute aktif
    $isBerandaActive = request()->routeIs('beranda') || request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->is('/') || request()->is('dashboard');
    $isMenuActive = request()->routeIs('menu.*') && !request()->routeIs('kategori-menu.*');
    $isKateringActive = request()->routeIs('katering.*') || request()->is('katering*');
    $isKasirActive = request()->routeIs('kasir.*') || request()->is('kasir*');
    $isKeuanganActive = request()->routeIs('keuangan.*') || request()->is('keuangan*');
    $isCmsActive = request()->routeIs('cms.*') || request()->is('cms*');
    $isMasterDataActive = request()->routeIs('master-data.*') || request()->routeIs('kategori-menu.*') || request()->is('kategori-menu*');
    $isKelolaAkunActive = request()->routeIs('kelola-akun.*') || request()->routeIs('users.*') || request()->is('kelola-akun*');

    // Class helper untuk pill menu
    $activeClass = 'bg-[#7B1113] text-white px-4 sm:px-5 py-1.5 rounded-full font-semibold text-sm shadow-xs transition duration-150 inline-flex items-center';
    $inactiveClass = 'text-stone-700 hover:text-[#7B1113] hover:bg-[#F5EFE6]/60 px-3.5 sm:px-4 py-1.5 rounded-full font-semibold text-sm transition duration-150 inline-flex items-center';
@endphp

<!-- Google Fonts: Cinzel & Plus Jakarta Sans -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    .font-brand-serif {
        font-family: 'Cinzel', Georgia, serif;
    }
    .font-brand-sub {
        font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<nav x-data="{ mobileOpen: false, userDropdown: false }" class="bg-[#FAF7F2] border-b border-[#EAE3D9]/90 shadow-[0_2px_8px_rgba(0,0,0,0.03)] font-sans sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- ==================================================================== -->
        <!-- BARIS ATAS: LOGO BRAND DI KIRI & AKSI ROLE / LOGIN DI KANAN          -->
        <!-- ==================================================================== -->
        <div class="flex items-center justify-between pt-3 pb-2">
            <!-- Brand Logo Khas Rumah Makan Padang Tiga Putri -->
            <a href="{{ Auth::check() ? route('dashboard') : route('beranda') }}" class="flex flex-col items-start group select-none">
                <!-- Icon Gonjong Khas Minangkabau (Emas) -->
                <div class="flex items-center mb-0.5">
                    <svg viewBox="0 0 200 60" class="h-6 sm:h-7 w-auto" xmlns="http://www.w3.org/2000/svg">
                        <g fill="#C59B27">
                            <path d="M 18,24 Q 44,48 64,15 Q 82,43 100,6 Q 118,43 136,15 Q 156,48 182,24 Q 166,42 154,43 L 46,43 Q 34,42 18,24 Z" />
                            <path d="M 48,45 L 152,45 L 148,54 L 52,54 Z" opacity="0.95" />
                            <rect x="62" y="47" width="6" height="4" fill="#FAF7F2" rx="1" />
                            <rect x="78" y="47" width="6" height="4" fill="#FAF7F2" rx="1" />
                            <rect x="97" y="47" width="6" height="4" fill="#FAF7F2" rx="1" />
                            <rect x="116" y="47" width="6" height="4" fill="#FAF7F2" rx="1" />
                            <rect x="132" y="47" width="6" height="4" fill="#FAF7F2" rx="1" />
                        </g>
                    </svg>
                </div>
                <!-- Nama Tiga Putri & Masakan Padang -->
                <span class="font-brand-serif font-black text-xl sm:text-2xl tracking-[0.14em] text-[#7B1113] leading-none uppercase">
                    Tiga Putri
                </span>
                <span class="font-brand-sub text-[8.5px] sm:text-[9.5px] font-bold tracking-[0.3em] text-stone-800 uppercase mt-0.5">
                    Masakan Padang
                </span>
            </a>

            <!-- Sisi Kanan: Sesuai Role (Guest: Tombol Login | Admin/Superadmin: Badge Shield & Dropdown) -->
            <div class="flex items-center gap-2 sm:gap-3">

                @if ($isGuest)
                    <!-- Tombol Login untuk Guest (Sesuai Desain Navbar Guest.png) -->
                    <a href="{{ route('login') }}" class="px-5 py-1.5 bg-[#F5EFE6] hover:bg-[#EFE5D7] active:bg-[#E6D9C8] text-[#7B1113] font-bold text-sm rounded-full transition shadow-xs">
                        Login
                    </a>
                @else
                    <!-- Badge Peran + Dropdown User Akun (Sesuai Desain Navbar Admin.png & Navbar Superadmin.png) -->
                    <div class="relative" @click.away="userDropdown = false">
                        <button
                            type="button"
                            @click="userDropdown = !userDropdown"
                            class="inline-flex items-center gap-2 px-4 py-1.5 bg-[#F5EFE6] hover:bg-[#EFE5D7] text-[#7B1113] font-bold text-sm rounded-full transition shadow-xs focus:outline-none"
                            aria-expanded="false"
                        >
                            <!-- Ikon Perisai (Shield) Khas Desain -->
                            <svg class="w-4 h-4 text-[#7B1113] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>

                            <!-- Teks Label Role -->
                            <span>{{ $isSuperAdmin ? 'Super Admin' : 'Admin' }}</span>

                            <!-- Arrow Down -->
                            <svg class="w-3.5 h-3.5 text-[#7B1113] transition-transform duration-200" :class="{ 'rotate-180': userDropdown }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Menu Dropdown -->
                        <div
                            x-show="userDropdown"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-56 rounded-2xl bg-white shadow-xl border border-stone-100 py-2 z-50"
                            style="display: none;"
                        >
                            @auth
                                <div class="px-4 py-2 border-b border-stone-100">
                                    <p class="text-xs font-semibold text-stone-400 uppercase tracking-wider">Masuk sebagai</p>
                                    <p class="text-sm font-bold text-stone-800 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-stone-500 truncate">{{ Auth::user()->email }}</p>
                                </div>

                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-stone-700 hover:bg-[#FAF7F2] hover:text-[#7B1113] transition">
                                    <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>Profil Saya</span>
                                </a>

                                <form method="POST" action="{{ route('logout') }}" class="border-t border-stone-100 mt-1">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition text-left">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        <span>Keluar (Logout)</span>
                                    </button>
                                </form>
                            @else
                                <div class="px-4 py-2 text-xs text-stone-500">Mode Pratinjau Role: <b>{{ $activeRole }}</b></div>
                            @endauth
                        </div>
                    </div>
                @endif

                <!-- Tombol Hamburger Menu (Khusus Tampilan Layar HP / Mobile) -->
                <button
                    type="button"
                    @click="mobileOpen = !mobileOpen"
                    class="sm:hidden p-2 rounded-xl text-stone-600 hover:text-[#7B1113] hover:bg-[#F5EFE6] focus:outline-none transition"
                    aria-label="Toggle Menu"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': mobileOpen, 'inline-flex': !mobileOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !mobileOpen, 'inline-flex': mobileOpen }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- ==================================================================== -->
        <!-- BARIS BAWAH: TAUTAN MENU NAVIGASI TINGKAT ROLE (DESKTOP)             -->
        <!-- ==================================================================== -->
        <div class="hidden sm:flex items-center gap-1.5 pt-1 pb-3 overflow-x-auto no-scrollbar">

            <!-- ------------------------------------------------------------- -->
            <!-- 1. ROLE: GUEST (Navbar Guest.png)                             -->
            <!-- Menu: Beranda, Menu                                           -->
            <!-- ------------------------------------------------------------- -->
            @if ($isGuest)
                <a href="{{ route('beranda') }}" class="{{ $isBerandaActive ? $activeClass : $inactiveClass }}">
                    Beranda
                </a>
                <a href="{{ route('menu.index') }}" class="{{ $isMenuActive ? $activeClass : $inactiveClass }}">
                    Menu
                </a>

            <!-- ------------------------------------------------------------- -->
            <!-- 2. ROLE: SUPER ADMIN (Navbar Superadmin.png)                  -->
            <!-- Menu: Beranda, Menu, Katering, Kasir, Keuangan, CMS,          -->
            <!--       Master Data, Kelola Akun                                -->
            <!-- ------------------------------------------------------------- -->
            @elseif ($isSuperAdmin)
                <a href="{{ route('dashboard') }}" class="{{ $isBerandaActive ? $activeClass : $inactiveClass }}">
                    Beranda
                </a>
                <a href="{{ route('menu.index') }}" class="{{ $isMenuActive ? $activeClass : $inactiveClass }}">
                    Menu
                </a>
                <a href="{{ route('katering.index') }}" class="{{ $isKateringActive ? $activeClass : $inactiveClass }}">
                    Katering
                </a>
                <a href="{{ route('kasir.index') }}" class="{{ $isKasirActive ? $activeClass : $inactiveClass }}">
                    Kasir
                </a>
                <a href="{{ route('keuangan.index') }}" class="{{ $isKeuanganActive ? $activeClass : $inactiveClass }}">
                    Keuangan
                </a>
                <a href="{{ route('cms.index') }}" class="{{ $isCmsActive ? $activeClass : $inactiveClass }}">
                    CMS
                </a>
                <a href="{{ route('kategori-menu.index') }}" class="{{ $isMasterDataActive ? $activeClass : $inactiveClass }}">
                    Master Data
                </a>
                <a href="{{ route('kelola-akun.index') }}" class="{{ $isKelolaAkunActive ? $activeClass : $inactiveClass }}">
                    Kelola Akun
                </a>

            <!-- ------------------------------------------------------------- -->
            <!-- 3. ROLE: ADMIN / PENJUAL (Navbar Admin/Penjual.png)           -->
            <!-- Menu: Beranda, Menu, Katering, Kasir, Keuangan                -->
            <!-- ------------------------------------------------------------- -->
            @else
                <a href="{{ route('dashboard') }}" class="{{ $isBerandaActive ? $activeClass : $inactiveClass }}">
                    Beranda
                </a>
                <a href="{{ route('menu.index') }}" class="{{ $isMenuActive ? $activeClass : $inactiveClass }}">
                    Menu
                </a>
                <a href="{{ route('katering.index') }}" class="{{ $isKateringActive ? $activeClass : $inactiveClass }}">
                    Katering
                </a>
                <a href="{{ route('kasir.index') }}" class="{{ $isKasirActive ? $activeClass : $inactiveClass }}">
                    Kasir
                </a>
                <a href="{{ route('keuangan.index') }}" class="{{ $isKeuanganActive ? $activeClass : $inactiveClass }}">
                    Keuangan
                </a>
            @endif

        </div>

    </div>

    <!-- ==================================================================== -->
    <!-- MENU MOBILE / RESPONSIVE (DRAWER SAAT LAYAR KECIL / TABLET KECIL)     -->
    <!-- ==================================================================== -->
    <div
        x-show="mobileOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="sm:hidden border-t border-[#EAE3D9] bg-[#FAF7F2] px-4 pt-3 pb-4 space-y-1.5"
        style="display: none;"
    >
        @if ($isGuest)
            <a href="{{ route('beranda') }}" class="{{ $isBerandaActive ? $activeClass : $inactiveClass }} w-full justify-start">
                Beranda
            </a>
            <a href="{{ route('menu.index') }}" class="{{ $isMenuActive ? $activeClass : $inactiveClass }} w-full justify-start">
                Menu
            </a>
            <div class="pt-2 border-t border-stone-200 mt-2">
                <a href="{{ route('login') }}" class="block text-center py-2 bg-[#8A1315] text-white font-bold rounded-xl text-sm shadow-xs">
                    Login Pengelola
                </a>
            </div>
        @elseif ($isSuperAdmin)
            <a href="{{ route('dashboard') }}" class="{{ $isBerandaActive ? $activeClass : $inactiveClass }} w-full justify-start">
                Beranda
            </a>
            <a href="{{ route('menu.index') }}" class="{{ $isMenuActive ? $activeClass : $inactiveClass }} w-full justify-start">
                Menu
            </a>
            <a href="{{ route('katering.index') }}" class="{{ $isKateringActive ? $activeClass : $inactiveClass }} w-full justify-start">
                Katering
            </a>
            <a href="{{ route('kasir.index') }}" class="{{ $isKasirActive ? $activeClass : $inactiveClass }} w-full justify-start">
                Kasir
            </a>
            <a href="{{ route('keuangan.index') }}" class="{{ $isKeuanganActive ? $activeClass : $inactiveClass }} w-full justify-start">
                Keuangan
            </a>
            <a href="{{ route('cms.index') }}" class="{{ $isCmsActive ? $activeClass : $inactiveClass }} w-full justify-start">
                CMS
            </a>
            <a href="{{ route('kategori-menu.index') }}" class="{{ $isMasterDataActive ? $activeClass : $inactiveClass }} w-full justify-start">
                Master Data
            </a>
            <a href="{{ route('kelola-akun.index') }}" class="{{ $isKelolaAkunActive ? $activeClass : $inactiveClass }} w-full justify-start">
                Kelola Akun
            </a>
        @else
            <a href="{{ route('dashboard') }}" class="{{ $isBerandaActive ? $activeClass : $inactiveClass }} w-full justify-start">
                Beranda
            </a>
            <a href="{{ route('menu.index') }}" class="{{ $isMenuActive ? $activeClass : $inactiveClass }} w-full justify-start">
                Menu
            </a>
            <a href="{{ route('katering.index') }}" class="{{ $isKateringActive ? $activeClass : $inactiveClass }} w-full justify-start">
                Katering
            </a>
            <a href="{{ route('kasir.index') }}" class="{{ $isKasirActive ? $activeClass : $inactiveClass }} w-full justify-start">
                Kasir
            </a>
            <a href="{{ route('keuangan.index') }}" class="{{ $isKeuanganActive ? $activeClass : $inactiveClass }} w-full justify-start">
                Keuangan
            </a>
        @endif
    </div>
</nav>
