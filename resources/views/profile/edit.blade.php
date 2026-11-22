<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-stone-900 tracking-tight">
                    Pengaturan Profil Pengelola
                </h1>
                <p class="text-xs sm:text-sm text-stone-500 mt-0.5">
                    Kelola data identitas dan amankan akun Anda dengan verifikasi OTP.
                </p>
            </div>
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#F5EFE6] text-[#7B1113] border border-[#EAE3D9]">
                    <svg class="w-3.5 h-3.5 text-[#7B1113]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>{{ $user->role === 'super_admin' ? 'Super Admin' : 'Admin / Penjual' }}</span>
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- ============================================================= -->
            <!-- KARTU RINGKASAN IDENTITAS PENGGUNA (HERO PROFILE SUMMARY)      -->
            <!-- ============================================================= -->
            <div class="bg-white rounded-2xl border border-stone-100 shadow-sm p-5 sm:p-6 flex flex-col md:flex-row md:items-center justify-between gap-5">
                <div class="flex items-center gap-4">
                    <!-- Avatar Inisial Bulat Khas Minang -->
                    <div class="w-16 h-16 sm:w-18 sm:h-18 rounded-2xl bg-gradient-to-br from-[#8A1315] to-[#5C0B0D] text-white flex items-center justify-center font-brand-serif font-black text-2xl sm:text-3xl shadow-md border-2 border-[#C59B27] shrink-0">
                        {{ strtoupper(substr($user->name ?? $user->username ?? 'U', 0, 1)) }}
                    </div>
                    <!-- Info Pengguna -->
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-lg sm:text-xl font-bold text-stone-900 leading-tight">
                                {{ $user->name }}
                            </h2>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                Akun Aktif
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-stone-500 mt-0.5">
                            Username: <span class="font-mono font-semibold text-stone-700">&#64;{{ $user->username }}</span>
                        </p>
                        <p class="text-xs text-stone-400 mt-0.5">
                            Email Terdaftar: <span class="text-stone-600 font-medium">{{ $user->email }}</span>
                        </p>
                    </div>
                </div>

                <!-- Info Hak Akses & Status Keamanan -->
                <div class="flex items-center gap-3 pt-4 md:pt-0 border-t md:border-t-0 border-stone-100">
                    <div class="px-4 py-2.5 rounded-xl bg-[#FAF7F2] border border-[#EAE3D9] text-left">
                        <span class="block text-[10px] uppercase font-bold text-stone-400 tracking-wider">Peran Akun</span>
                        <span class="text-xs sm:text-sm font-bold text-[#7B1113]">
                            {{ $user->role === 'super_admin' ? 'Super Admin' : 'Admin / Penjual' }}
                        </span>
                    </div>

                    <div class="px-4 py-2.5 rounded-xl bg-[#FAF7F2] border border-[#EAE3D9] text-left">
                        <span class="block text-[10px] uppercase font-bold text-stone-400 tracking-wider">Proteksi Sandi</span>
                        <span class="text-xs sm:text-sm font-bold text-emerald-700 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Verifikasi OTP
                        </span>
                    </div>
                </div>
            </div>

            <!-- ============================================================= -->
            <!-- GRID 2 KOLOM: INFORMASI PROFIL & GANTI KATA SANDI DENGAN OTP   -->
            <!-- ============================================================= -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- Kolom Kiri: Informasi Profil (5 Kolom di Desktop) -->
                <div class="lg:col-span-5 bg-white rounded-2xl border border-stone-100 shadow-sm p-6 sm:p-7">
                    @include('profile.partials.update-profile-information-form')
                </div>

                <!-- Kolom Kanan: Ganti Password dengan OTP (7 Kolom di Desktop) -->
                <div class="lg:col-span-7 bg-white rounded-2xl border border-stone-100 shadow-sm p-6 sm:p-7">
                    @include('profile.partials.update-password-form')
                </div>

            </div>

            <!-- ============================================================= -->
            <!-- ZONA HAPUS AKUN (DANGER ZONE)                                  -->
            <!-- ============================================================= -->
            <div class="bg-white rounded-2xl border border-red-100 shadow-sm p-6 sm:p-7">
                <div class="max-w-2xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
