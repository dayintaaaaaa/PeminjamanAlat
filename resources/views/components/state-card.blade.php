@props(['title', 'value', 'icon' => null, 'tone' => 'green', 'badge' => null])

@php
    $tones = [
        'green' => ['icon' => 'bg-green-100 text-green-600', 'badge' => 'bg-green-100 text-green-700'],
        'blue'  => ['icon' => 'bg-blue-100 text-blue-600',   'badge' => 'bg-blue-100 text-blue-700'],
        'red'   => ['icon' => 'bg-red-100 text-red-600',     'badge' => 'bg-red-100 text-red-600'],
    ];
    $t = $tones[$tone] ?? $tones['green'];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl shadow-md p-5']) }}>
    <div class="flex items-center justify-between">
        <p class="text-xs font-semibold text-gray-500 uppercase">{{ $title }}</p>
        @if ($icon)
            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $t['icon'] }}">
                <i class="bi {{ $icon }}"></i>
            </div>
        @endif
    </div>

    <p class="text-2xl font-bold text-gray-900 mt-2">
        {{ $value }} <span class="text-xs font-normal text-gray-500">Items</span>
    </p>

    @if ($badge)
        <span class="inline-block mt-2 text-[11px] font-medium rounded px-2 py-0.5 {{ $t['badge'] }}">{{ $badge }}</span>
    @endif
</div>