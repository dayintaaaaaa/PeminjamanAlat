@php
$role = auth()->user()->getRoleNames()->first();

$menus = match ($role) {
    'admin' => [
        ['icon' => 'bi-bar-chart-fill', 'label' => 'Dashboard', 'url' => '/dashboard', 'match' => 'dashboard'],
        ['icon' => 'bi-person-fill', 'label' => 'User Management', 'url' => '/users', 'match' => 'users*'],
        ['icon' => 'bi-archive-fill', 'label' => 'Master Data', 'children' => [
        ['label' => 'Tools Category', 'url' => '/tool-categories', 'match' => 'tool-categories*'],
        ['label' => 'Tools Data', 'url' => '/tools-data', 'match' => 'tools*'],
    ]],
        ['icon' => 'bi-arrow-left-right', 'label' => 'Transaction', 'url' => '/transactions', 'match' => 'transactions*'],
        ['icon' => 'bi-clock-history', 'label' => 'Activity Log', 'url' => '/activity-logs', 'match' => 'activity-logs'],
    ],
    'petugas' => [
        ['icon' => 'bi-archive-fill', 'label' => 'Tools Data', 'url' => '/tools-data', 'match' => 'tools*'],
        ['icon' => 'bi-box-arrow-in-down', 'label' => 'Borrowed', 'url' => '/borrowed', 'match' => 'borrowed*'],
        ['icon' => 'bi-box-arrow-up-left', 'label' => 'Return', 'url' => '/return', 'match' => 'return*'],
    ],
    'peminjam' => [
        ['icon' => 'bi-archive-fill', 'label' => 'Tools Data', 'url' => '/tools-data', 'match' => 'tools*'],
        ['icon' => 'bi-arrow-left-right', 'label' => 'My Borrowed', 'url' => '/my-borrowed', 'match' => 'my-borrowed*'],
    ],
    default => [],  
};
@endphp

<aside class="w-64 shrink-0 sticky top-0 h-screen overflow-y-auto bg-[var(--c-primary)] text-white flex flex-col">
    <div class="px-6 py-6 font-bold text-[15px] flex items-center gap-2 whitespace-nowrap">
        <i class="bi bi-wrench-adjustable"></i> Sistem Peminjaman Alat
    </div>

    <nav class="flex-1 mt-6 text-[13px] font-semibold">
        @foreach ($menus as $m)
        @if (isset($m['children']))
        <div class="px-6 py-3 flex items-center gap-3">
            <i class="bi {{ $m['icon'] }}"></i> {{ $m['label'] }}
        </div>
        @foreach ($m['children'] as $c)
        <a href="{{ $c['url'] }}" class="block pl-14 px-6 py-3 {{ request()->is($c['match']) ? 'bg-[var(--c-active)]' : 'hover:bg-[var(--c-active)]/60' }}">
            {{ $c['label'] }}
        </a>
        @endforeach
        @else
        <a href="{{ $m['url'] }}" class="flex items-center gap-3 px-6 py-3 {{ request()->is($m['match']) ? 'bg-[var(--c-active)]' : 'hover:bg-[var(--c-active)]/60' }}">
            <i class="bi {{ $m['icon'] }}"></i> {{ $m['label'] }}
        </a>
        @endif
        @endforeach
    </nav>

    <div class="p-4" x-data="{ open: false }" @click.outside="open = false">
        {{-- tombol Log Out: muncul kalau titik tiga diklik --}}
        <div x-show="open" x-cloak x-transition class="mb-3">
            <button type="button"
                @click="open = false; $dispatch('open-modal', 'confirm-logout')"
                class="w-full bg-white text-red-600 font-semibold text-sm rounded-lg py-2 flex items-center justify-center gap-2 border hover:bg-red-50">
                <i class="bi bi-box-arrow-right"></i> Log Out
            </button>
        </div>

        {{-- kartu user --}}
        <div class="bg-[var(--c-primary-dark)] rounded-lg p-3 flex items-center gap-3">
            <i class="bi bi-person-circle text-2xl"></i>
            <div class="flex-1 leading-tight">
                <div class="font-semibold text-sm">{{ auth()->user()->name }}</div>
                <div class="text-xs capitalize">{{ $role }}</div>
            </div>
            <button type="button" @click="open = !open" class="px-1">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
        </div>
    </div>
</aside>