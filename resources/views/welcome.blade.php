<x-app-layout>
    <div class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow py-8 sm:py-12">
        <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row shadow-sm rounded-xl overflow-hidden">
            <div class="text-[13px] leading-[20px] flex-1 p-6 pb-6 lg:p-20 lg:pb-10 bg-white text-stone-800 border border-stone-200 lg:border-r-0 rounded-bl-lg rounded-br-lg lg:rounded-tl-lg lg:rounded-br-none">
                <h1 class="mb-1 font-semibold text-lg text-stone-900">Selamat Datang di Rumah Makan Tiga Putri</h1>
                

                <div class="pt-4 border-t border-stone-200 flex items-center justify-between text-xs text-stone-400">
                   
                    <span>Laravel v{{ app()->version() }}</span>
                </div>
            </div>

           
        </main>
    </div>
</x-app-layout>
