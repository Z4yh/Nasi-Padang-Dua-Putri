<x-app-layout>
    {{-- ==================================================================== --}}
    {{-- TAMPILAN BERANDA PUBLIK (Pengunjung / Tamu / Pelanggan)               --}}
    {{-- Menggunakan Navbar Guest (Beranda, Menu, Tombol Login)                --}}
    {{-- Tampilan etalase identik dengan Beranda Admin tanpa data rahasia      --}}
    {{-- ==================================================================== --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        <!-- Part 1: Hero, Banner Foto Warung, Pesan Online, & Menu Favorit -->
        @include('beranda.partials.hero-dan-menu')

        <!-- Part 2: Jam Buka, Layanan, & Lokasi Sukatani Bekasi -->
        @include('beranda.partials.jam-dan-lokasi')

    </div>
</x-app-layout>
