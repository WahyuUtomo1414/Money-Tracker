<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-gray-100 dark:border-gray-800 pb-5">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Target Tabungan (Goals)</p>
                <div
                    x-data="{
                        current: 0,
                        target: {{ (int) $totalGoals }},
                        animate() {
                            const duration = 600
                            const start = performance.now()
                            const step = (now) => {
                                const progress = Math.min((now - start) / duration, 1)
                                this.current = Math.round(this.target * progress)
                                if (progress < 1) requestAnimationFrame(step)
                            }
                            requestAnimationFrame(step)
                        },
                    }"
                    x-init="animate()"
                    class="mt-1 flex items-baseline gap-2"
                >
                    <span class="text-3xl font-extrabold tracking-tight text-gray-950 dark:text-white" x-text="current"></span>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">target aktif ditemukan</span>
                </div>
            </div>
            <div>
                <a 
                    href="{{ \App\Filament\Resources\Goals\GoalResource::getUrl('index') }}" 
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                >
                    Lihat Semua
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
        </div>

        @if(empty($goals) || $goals->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <div class="rounded-full bg-gray-50 dark:bg-gray-800/50 p-3 text-gray-400">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-sm font-semibold text-gray-900 dark:text-white">Belum ada target</h3>
                <p class="mt-1 text-xs text-gray-500">Mulai buat target tabungan Anda untuk memantau perkembangan dana.</p>
            </div>
        @else
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach ($goals as $goal)
                    @php
                        $isAchieved = $goal['current_amount'] >= $goal['target_amount'];
                        $editUrl = \App\Filament\Resources\Goals\GoalResource::getUrl('edit', ['record' => $goal['id']]);
                    @endphp
                    <a 
                        href="{{ $editUrl }}"
                        class="group relative flex flex-col justify-between overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5 shadow-sm hover:shadow-md hover:border-gray-300 dark:hover:border-gray-700 hover:-translate-y-1 transition-all duration-300 cursor-pointer"
                    >
                        <div>
                            <!-- Header: Title & Status -->
                            <div class="flex items-start justify-between gap-4">
                                <div class="font-bold text-base text-gray-950 dark:text-white group-hover:text-[#112E81] dark:group-hover:text-blue-400 transition-colors line-clamp-1">
                                    {{ $goal['name'] }}
                                </div>
                                
                                @if($isAchieved)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 dark:bg-emerald-500/10 px-2 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-400 ring-1 ring-inset ring-emerald-600/10 dark:ring-emerald-500/20">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                        Tercapai
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-blue-50 dark:bg-blue-500/10 px-2 py-1 text-xs font-semibold text-blue-700 dark:text-blue-400 ring-1 ring-inset ring-blue-600/10 dark:ring-blue-500/20">
                                        Dalam Proses
                                    </span>
                                @endif
                            </div>

                            <!-- Meta Info (Wallet & Date) -->
                            <div class="mt-3 flex flex-wrap gap-2">
                                <!-- Wallet Badge -->
                                <span class="inline-flex items-center gap-1.5 rounded-md bg-gray-50 dark:bg-gray-800/80 px-2 py-1 text-xs font-medium text-gray-600 dark:text-gray-400">
                                    <svg class="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6A2.25 2.25 0 0118.75 20H5.25A2.25 2.25 0 013 17.75V12m18 0V9.75A2.25 2.25 0 0018.75 7.5H5.25A2.25 2.25 0 003 9.75V12m9-3h.008v.008H12V9zm3 0h.008v.008H15V9z" />
                                    </svg>
                                    {{ $goal['wallet'] }}
                                </span>
                                
                                <!-- Date Badge -->
                                <span class="inline-flex items-center gap-1.5 rounded-md bg-gray-50 dark:bg-gray-800/80 px-2 py-1 text-xs font-medium text-gray-600 dark:text-gray-400">
                                    <svg class="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zM14.25 15h.008v.008H14.25V15zm0 2.25h.008v.008H14.25v-.008zM16.5 15h.008v.008H16.5V15zm0 2.25h.008v.008H16.5v-.008z" />
                                    </svg>
                                    {{ $goal['target_date'] }}
                                </span>
                            </div>
                        </div>

                        <!-- Progress Bar & Amounts -->
                        <div class="mt-6">
                            <div class="flex items-end justify-between mb-2">
                                <div>
                                    <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 block uppercase tracking-wider">Terkumpul</span>
                                    <span class="text-lg font-bold text-[#112E81] dark:text-blue-400">Rp {{ number_format($goal['current_amount'], 0, ',', '.') }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 block uppercase tracking-wider">Target</span>
                                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Rp {{ number_format($goal['target_amount'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                            
                            <!-- Progress Line -->
                            <div class="relative w-full">
                                <div class="h-3 w-full rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                                    <div
                                        class="h-full rounded-full bg-gradient-to-r {{ $isAchieved ? 'from-emerald-500 to-teal-400' : 'from-[#112E81] to-blue-500' }} transition-all duration-700 ease-out"
                                        style="width: {{ $goal['progress'] }}%;"
                                    ></div>
                                </div>
                                <!-- Floating Percent Badge inside Progress Line if large enough or just at the side -->
                                <div class="mt-2 flex items-center justify-end">
                                    <span class="inline-flex items-center rounded-md bg-gray-50 dark:bg-gray-800/80 px-2 py-0.5 text-xs font-semibold {{ $isAchieved ? 'text-emerald-700 dark:text-emerald-400' : 'text-gray-600 dark:text-gray-400' }}">
                                        {{ $goal['progress'] }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
