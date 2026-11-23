<x-app-layout>
    @if (Auth::user()->role === 'pelanggan')
        {{-- ==================================================================== --}}
        {{-- TAMPILAN DASHBOARD PELANGGAN (Sesuai aslinya, tidak diubah)            --}}
        {{-- ==================================================================== --}}
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        {{ __("You're logged in!") }}
                    </div>
                </div>
            </div>
        </div>

    @elseif (isset($title) || (!request()->routeIs('dashboard') && !request()->routeIs('admin.dashboard')))
        {{-- ==================================================================== --}}
        {{-- TAMPILAN KERANGKA PLACEHOLDER (KASIR, KEUANGAN, CMS, KELOLA AKUN)    --}}
        {{-- Agar halaman menu lain tidak tertimpa oleh tampilan Beranda          --}}
        {{-- ==================================================================== --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-[#EAE3D9] shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-5 border-b border-stone-200 gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-stone-900 font-brand-serif tracking-wide">
                            {{ $title ?? 'Menu Operasional' }}
                        </h1>
                        <p class="text-sm text-stone-500 mt-1 font-brand-sub">
                            Rumah Makan Padang Tiga Putri &bull; Sukatani, Bekasi
                        </p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#FAF0D9] text-[#8C6414] border border-[#F3E2B8] self-start sm:self-auto">
                        <span class="w-2 h-2 rounded-full bg-[#C59B27]"></span>
                        Fitur Dalam Pengembangan
                    </span>
                </div>

                <div class="mt-8 py-16 px-6 border-2 border-dashed border-[#EAE3D9] rounded-2xl text-center bg-[#FAF7F2] flex flex-col items-center justify-center">
                    <div class="w-16 h-16 rounded-full bg-[#F5EFE6] flex items-center justify-center mb-3 shadow-inner">
                        <svg class="w-8 h-8 text-[#7B1113]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-stone-800">
                        Halaman {{ $title ?? 'Operasional' }}
                    </h3>
                    <p class="text-xs sm:text-sm text-stone-500 max-w-md mt-1">
                        Kerangka tampilan telah disiapkan. Logika dan alur data pada halaman ini akan dikerjakan oleh rekan tim yang bertugas.
                    </p>

                    {{-- ISI HALAMAN: logika dikerjakan teman --}}
                    <div class="mt-6">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2 bg-[#7B1113] hover:bg-[#600D0F] text-white text-xs font-bold rounded-xl shadow-xs transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>

    @else
        {{-- ==================================================================== --}}
        {{-- TAMPILAN BERANDA ADMIN (super_admin & penjual)                        --}}
        {{-- ==================================================================== --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

            {{-- RUANG KOSONG: Blok Ringkasan Hari Ini (Dikerjakan di sesi lain) --}}

            <!-- Part 1: Hero, Banner Foto Warung, Pesan Online, & Menu Favorit -->
            @include('beranda.partials.hero-dan-menu')

            <!-- Part 2: Jam Buka, Layanan, & Lokasi Sukatani Bekasi -->
            @include('beranda.partials.jam-dan-lokasi')

        </div>
    @endif
</x-app-layout>
