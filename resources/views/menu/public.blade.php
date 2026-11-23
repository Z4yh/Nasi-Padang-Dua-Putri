<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h1 class="text-xl font-bold text-gray-900 mb-2">
                    Tampilan Menu Pengunjung  
                </h1>
                <p class="text-sm text-gray-600 mb-6">
                    Halaman ini bisa diakses tanpa login. Desain tampilan menu final akan dikerjakan oleh tim yang bertugas.
                </p>


                  

                    @if ($menuList->hasPages())
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            {{ $menuList->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
