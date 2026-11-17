<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Data Master Katalog Menu') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Kelola daftar menu masakan, harga, foto, dan status ketersediaan stok
                </p>
            </div>
            <a href="{{ route('menu.create') }}"
               class="inline-flex items-center px-4 py-2 bg-red-700 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-red-800 focus:bg-red-800 active:bg-red-900 transition ease-in-out duration-150 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Menu Baru
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Alert -->
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-lg shadow-sm flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-r-lg shadow-sm flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Search and Filter Card -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('menu.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- Search Input -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Cari nama menu..."
                               class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-red-500 focus:border-red-500">
                    </div>

                    <!-- Kategori Filter -->
                    <div>
                        <select name="id_kategori_menu"
                                class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-red-500 focus:border-red-500">
                            <option value="">-- Semua Kategori --</option>
                            @foreach ($kategoriList as $kategori)
                                <option value="{{ $kategori->id_kategori_menu }}" {{ request('id_kategori_menu') == $kategori->id_kategori_menu ? 'selected' : '' }}>
                                    {{ $kategori->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Stok Filter -->
                    <div>
                        <select name="status_stok"
                                class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-red-500 focus:border-red-500">
                            <option value="">-- Semua Status Stok --</option>
                            <option value="tersedia" {{ request('status_stok') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="habis" {{ request('status_stok') === 'habis' ? 'selected' : '' }}>Habis</option>
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center gap-2">
                        <button type="submit"
                                class="flex-1 px-4 py-2 bg-gray-800 text-white rounded-lg text-sm font-medium hover:bg-gray-700 transition">
                            Terapkan Filter
                        </button>
                        @if(request('search') || request('id_kategori_menu') || request('status_stok'))
                            <a href="{{ route('menu.index') }}"
                               class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-gray-700 uppercase text-xs font-semibold">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left w-16">No</th>
                                <th scope="col" class="px-6 py-4 text-center w-24">Foto</th>
                                <th scope="col" class="px-6 py-4 text-left">Nama & Kategori</th>
                                <th scope="col" class="px-6 py-4 text-right">Harga</th>
                                <th scope="col" class="px-6 py-4 text-center">Status Stok</th>
                                <th scope="col" class="px-6 py-4 text-center w-40">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($menuList as $menu)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-gray-500 font-mono">
                                        {{ ($menuList->currentPage() - 1) * $menuList->perPage() + $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($menu->foto_menu && \Illuminate\Support\Facades\Storage::disk('public')->exists($menu->foto_menu))
                                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($menu->foto_menu) }}"
                                                 alt="{{ $menu->nama_menu }}"
                                                 class="w-16 h-16 object-cover rounded-lg shadow-sm border border-gray-200 mx-auto" />
                                        @else
                                            <div class="w-16 h-16 bg-gray-100 border border-dashed border-gray-300 rounded-lg flex flex-col items-center justify-center text-gray-400 mx-auto">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span class="text-[9px] mt-0.5">No Foto</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900 text-base">
                                            {{ $menu->nama_menu }}
                                        </div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-50 text-red-700 border border-red-100">
                                                {{ $menu->kategori->nama_kategori ?? 'Tanpa Kategori' }}
                                            </span>
                                            <span class="text-xs text-gray-400 font-mono">
                                                /{{ $menu->slug_menu }}
                                            </span>
                                        </div>
                                        @if($menu->deskripsi)
                                            <p class="text-xs text-gray-500 mt-1 line-clamp-2 max-w-md">
                                                {{ $menu->deskripsi }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono font-bold text-gray-900">
                                        Rp {{ number_format($menu->harga, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="inline-flex flex-col items-center gap-1.5">
                                            @if($menu->status_stok === 'tersedia')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                                    <span class="w-1.5 h-1.5 mr-1.5 bg-emerald-500 rounded-full"></span>
                                                    Tersedia
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">
                                                    <span class="w-1.5 h-1.5 mr-1.5 bg-rose-500 rounded-full"></span>
                                                    Habis
                                                </span>
                                            @endif

                                            <!-- Quick Toggle Button -->
                                            <form method="POST" action="{{ route('menu.toggle-status', $menu) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="text-[11px] text-gray-500 hover:text-red-700 underline transition cursor-pointer">
                                                    Ubah ke {{ $menu->status_stok === 'tersedia' ? 'Habis' : 'Tersedia' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="inline-flex items-center justify-center gap-2">
                                            <a href="{{ route('menu.edit', $menu) }}"
                                               class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-md transition"
                                               title="Edit Menu">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form method="POST"
                                                  action="{{ route('menu.destroy', $menu) }}"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu \'{{ $menu->nama_menu }}\'?');"
                                                  class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-md transition"
                                                        title="Hapus Menu">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                            <p class="text-base font-medium text-gray-700">Belum ada menu masakan.</p>
                                            <p class="text-xs text-gray-500 mt-1">Silakan klik tombol "Tambah Menu Baru" untuk menambahkan hidangan Padang ke katalog.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($menuList->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        {{ $menuList->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
