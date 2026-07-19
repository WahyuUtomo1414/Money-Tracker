@php
    $state = $getState();
    $imageUrl = filled($state) ? \Illuminate\Support\Facades\Storage::disk('public')->url($state) : null;
@endphp

@if ($imageUrl)
    <div x-data="{ open: false }">
        <button
            type="button"
            class="group block overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
            x-on:click="open = true"
        >
            <img
                src="{{ $imageUrl }}"
                alt="Bukti Gambar"
                class="h-44 w-full max-w-md object-cover transition duration-200 group-hover:scale-[1.02]"
            >
        </button>

        <div
            x-cloak
            x-show="open"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-6"
            x-on:keydown.escape.window="open = false"
        >
            <button
                type="button"
                class="absolute inset-0"
                aria-label="Tutup preview bukti gambar"
                x-on:click="open = false"
            ></button>

            <div class="relative z-10 max-h-[90vh] max-w-5xl overflow-hidden rounded-2xl bg-white p-4 shadow-2xl">
                <div class="mb-3 flex items-center justify-between gap-4">
                    <p class="text-sm font-semibold text-gray-950">Bukti Gambar</p>

                    <button
                        type="button"
                        class="rounded-lg px-3 py-1.5 text-sm font-medium text-gray-600 hover:bg-gray-100"
                        x-on:click="open = false"
                    >
                        Tutup
                    </button>
                </div>

                <img
                    src="{{ $imageUrl }}"
                    alt="Bukti Gambar"
                    class="max-h-[78vh] w-auto max-w-full rounded-xl object-contain"
                >
            </div>
        </div>
    </div>
@else
    <span class="text-sm text-gray-500">-</span>
@endif
