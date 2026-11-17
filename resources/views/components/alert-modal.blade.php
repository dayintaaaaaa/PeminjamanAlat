@props([
    'name',
    'type' => 'success',   // pilihan: confirm, success, error
    'title' => '',
    'message' => '',
])

@php
    $styles = [
        'confirm' => ['bi-exclamation-lg', 'bg-[var(--c-primary)]'],
        'success' => ['bi-check-lg',       'bg-[var(--c-primary)]'],
        'error'   => ['bi-x-lg',           'bg-red-600'],
        'approve' => ['bi-check-lg',       'bg-[var(--c-primary)]'],
        'reject'  => ['bi-x-lg',           'bg-[var(--c-primary)]'],
        'return'  => ['bi-box-seam',       'bg-[var(--c-primary)]'],
    ];
    [$icon, $color] = $styles[$type];
@endphp

<x-modal :name="$name" maxWidth="sm">
    <div class="p-6 text-center">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full {{ $color }} text-white">
            <i class="bi {{ $icon }} text-3xl"></i>
        </div>

        <h2 class="mt-4 text-lg font-bold text-gray-900">{{ $title }}</h2>
        <p class="mt-1 text-xs text-gray-500">{{ $message }}</p>

        @if ($slot->isNotEmpty())
            <div class="mt-5 flex justify-center gap-3">
                {{ $slot }}
            </div>
        @endif
    </div>
</x-modal>