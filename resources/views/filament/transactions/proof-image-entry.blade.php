@php
    $state = $getState();
    $imageUrl = filled($state) ? \Illuminate\Support\Facades\Storage::disk('public')->url($state) : null;
    $dialogId = 'proof-image-preview-' . uniqid();
@endphp

@if ($imageUrl)
    <div>
        <button
            type="button"
            onclick="document.getElementById('{{ $dialogId }}').showModal()"
            style="display: block; overflow: hidden; width: 100%; max-width: 420px; border: 1px solid #e5e7eb; border-radius: 12px; background: #ffffff; box-shadow: 0 1px 2px rgba(15, 23, 42, .08); cursor: pointer;"
        >
            <img
                src="{{ $imageUrl }}"
                alt="Bukti Gambar"
                style="display: block; width: 100%; height: 180px; object-fit: cover;"
            >
        </button>

        <dialog
            id="{{ $dialogId }}"
            style="width: min(92vw, 920px); max-height: 90vh; border: 0; border-radius: 16px; padding: 16px; box-shadow: 0 24px 80px rgba(15, 23, 42, .32);"
        >
            <style>
                #{{ $dialogId }} {
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    margin: 0;
                }

                #{{ $dialogId }}::backdrop {
                    background: rgba(15, 23, 42, .72);
                }
            </style>

            <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 12px;">
                <strong style="font-size: 14px; color: #0f172a;">Bukti Gambar</strong>

                <form method="dialog">
                    <button
                        type="submit"
                        style="border: 0; border-radius: 8px; padding: 8px 12px; color: #475569; background: #f1f5f9; cursor: pointer;"
                    >
                        Tutup
                    </button>
                </form>
            </div>

            <img
                src="{{ $imageUrl }}"
                alt="Bukti Gambar"
                style="display: block; max-width: 100%; max-height: 76vh; margin: 0 auto; border-radius: 12px; object-fit: contain;"
            >
        </dialog>
    </div>
@else
    <span class="text-sm text-gray-500">-</span>
@endif
