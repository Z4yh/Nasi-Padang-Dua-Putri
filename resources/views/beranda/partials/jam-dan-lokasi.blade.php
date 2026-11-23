<!-- ================================================================ -->
<!-- JAM BUKA, LAYANAN & LOKASI (DESKTOP: GRID 2 KOLOM BERDAMPINGAN)   -->
<!-- ================================================================ -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
    
    <!-- Sisi Kiri: Jam Buka & Layanan -->
    <div class="space-y-3">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-[#7B1113]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h2 class="font-bold text-sm sm:text-base text-stone-800">
                Jam Buka & Layanan
            </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Jam Buka -->
            <div class="bg-white p-4 rounded-2xl border border-[#EAE3D9] shadow-2xs space-y-1.5">
                <div class="flex items-center gap-1.5 text-stone-500 text-[10px] sm:text-[11px] font-bold tracking-wider uppercase">
                    <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Setiap Hari</span>
                </div>
                <div class="font-bold text-stone-900 text-base sm:text-lg">
                    08.00 - 22.00
                </div>
                <div class="text-[11px] text-stone-500">
                    WIB (Tanpa Libur)
                </div>
            </div>

            <!-- Layanan -->
            <div class="bg-white p-4 rounded-2xl border border-[#EAE3D9] shadow-2xs space-y-1.5">
                <div class="flex items-center gap-1.5 text-stone-500 text-[10px] sm:text-[11px] font-bold tracking-wider uppercase">
                    <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z" />
                    </svg>
                    <span>Layanan</span>
                </div>
                <div class="font-bold text-stone-900 text-base sm:text-lg truncate">
                    Dine-in & Katering
                </div>
                <div class="text-[11px] text-stone-500">
                    Katering & Pesta
                </div>
            </div>
        </div>
    </div>

    <!-- Sisi Kanan: Lokasi Rumah Makan -->
    <div class="space-y-3">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-[#7B1113]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <h2 class="font-bold text-sm sm:text-base text-stone-800">
                Lokasi Rumah Makan
            </h2>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-[#EAE3D9] shadow-2xs space-y-3">
            <!-- Alamat Lengkap -->
            <p class="text-xs sm:text-[13px] text-stone-600 leading-relaxed">
                Jl. Raya Bumi Kahuripan Indah No.1 Blok A1, Sukamanah, Kec. Sukatani, Kabupaten Bekasi, Jawa Barat 17631
            </p>

            <!-- Peta Visual Ringkas Sukatani Bekasi -->
            <div class="w-full h-32 sm:h-36 rounded-xl overflow-hidden border border-[#EAE3D9] bg-[#EDE6DC] relative flex items-center justify-center">
                <div class="absolute inset-0 opacity-40 bg-[radial-gradient(#C59B27_1px,transparent_1px)] [background-size:16px_16px]"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/95 border border-[#C59B27]/40 shadow-sm text-stone-800 text-xs font-bold">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-600 animate-pulse"></span>
                        <span>RM Tiga Putri Sukatani</span>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi: Buka di Google Maps & WhatsApp -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                <!-- Tombol Google Maps -->
                <a href="https://maps.google.com/?q=RM+Tiga+Putri+Sukatani+Bekasi" target="_blank" rel="noopener noreferrer" class="py-2.5 px-3 bg-[#FAF7F2] hover:bg-[#F3ECE0] active:bg-[#EAE1D3] text-stone-800 font-bold text-xs sm:text-sm rounded-xl border border-[#E0D6C8] transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-[#7B1113] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    <span>Buka Google Maps</span>
                </a>

                <!-- Tombol WhatsApp Reservasi -->
                <a href="https://wa.me/6281234567890?text=Halo%20Rumah%20Makan%20Tiga%20Putri,%20saya%20ingin%20reservasi%20/%20pesan%20nasi%20kotak" target="_blank" rel="noopener noreferrer" class="py-2.5 px-3 bg-[#7B1113] hover:bg-[#600D0F] active:bg-[#4E090B] text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-emerald-300 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.072.043.419-.101.824z" />
                    </svg>
                    <span>WhatsApp Reservasi</span>
                </a>
            </div>
        </div>
    </div>

</div>
