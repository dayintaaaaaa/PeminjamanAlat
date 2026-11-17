<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    @php
        // data contoh, nanti diganti data dari database
        $days       = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $borrowData = [12, 15, 22, 18, 20, 8, 3];
        $returnData = [8, 12, 16, 14, 17, 6, 2];
        $maxBar     = max($borrowData);

        $topTools = [
            ['name' => 'Digital Multimeter', 'code' => 'MT-01', 'total' => 34],
            ['name' => 'Soldering Station',  'code' => 'ED-01', 'total' => 28],
            ['name' => 'Wire Stripper',      'code' => 'HT-01', 'total' => 19],
        ];
        $maxTop    = max(array_column($topTools, 'total'));
        $rankStyle = ['bg-amber-100 text-amber-700', 'bg-gray-100 text-gray-600', 'bg-orange-100 text-orange-700'];
    @endphp

    <h1 class="text-2xl font-bold">Dashboard</h1>
    <p class="text-sm text-gray-600 mb-6">Welcome back, {{ auth()->user()->name }}! Here is today's equipment management summary.</p>

    {{-- 4 kartu --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
        <x-state-card title="Total Tools" value="95" icon="bi-box-seam" badge="4 Registered Categories" />
        <x-state-card title="Available Tools" value="74" icon="bi-check-circle" badge="Available" />
        <x-state-card title="Borrowed" value="18" icon="bi-arrow-left-right" tone="blue" badge="Active Lending" />
        <x-state-card title="Overdue" value="3" icon="bi-exclamation-triangle" tone="red" badge="Overdue" />
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-5 mt-5">

        {{-- grafik --}}
        <x-card class="xl:col-span-2">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold">Borrow & Return Statistic</h2>
                <span class="text-xs font-medium rounded-lg px-3 py-1 bg-green-100 text-green-700">
                    <i class="bi bi-graph-up-arrow"></i> +14% this week
                </span>
            </div>

            <div class="flex items-center gap-4 text-xs text-gray-600 mb-4">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-[var(--c-primary)]"></span> Borrowing</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-[#5FD3B0]"></span> Return</span>
            </div>

            <div class="flex items-end justify-between gap-2 bg-gray-50 rounded-xl px-3 pt-4 pb-2">
                @foreach ($days as $i => $day)
                    <div class="flex-1 flex flex-col items-center justify-end">
                        <div class="h-28 flex items-end gap-1">
                            <div class="w-3 rounded-t bg-[var(--c-primary)]" @style(['height: ' . round($borrowData[$i] / $maxBar * 100) . '%'])></div>
                            <div class="w-3 rounded-t bg-[#5FD3B0]" @style(['height: ' . round($returnData[$i] / $maxBar * 100) . '%'])></div>
                        </div>
                        <span class="text-[11px] text-gray-500 mt-2">{{ $day }}</span>
                    </div>
                @endforeach
            </div>
        </x-card>
    </div>
</x-app-layout>