<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    @php
        $rankStyle = ['bg-amber-100 text-amber-700', 'bg-gray-100 text-gray-600', 'bg-orange-100 text-orange-700'];
    @endphp

    <h1 class="text-2xl font-bold">Dashboard</h1>
    <p class="text-sm text-gray-600 mb-6">Welcome back, {{ auth()->user()->name }}! Here is today's equipment management summary.</p>

    {{-- 4 kartu --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
        <x-state-card title="Total Tools" :value="$totalTools" icon="bi-box-seam" :badge="$categoryCount . ' Registered Categories'" />
        <x-state-card title="Available Tools" :value="$availableTools" icon="bi-check-circle" badge="Available" />
        <x-state-card title="Borrowed" :value="$borrowedTools" icon="bi-arrow-left-right" tone="blue" badge="Active Lending" />
        <x-state-card title="Overdue" :value="$overdueCount" icon="bi-exclamation-triangle" tone="red" badge="Overdue" />
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-5 mt-5">

        {{-- grafik --}}
        <x-card class="xl:col-span-2">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold">Borrow & Return Statistic</h2>

                @if ($trend === null)
                    <span class="text-xs font-medium rounded-lg px-3 py-1 bg-gray-100 text-gray-600">
                        No data last week
                    </span>
                @elseif ($trend >= 0)
                    <span class="text-xs font-medium rounded-lg px-3 py-1 bg-green-100 text-green-700">
                        <i class="bi bi-graph-up-arrow"></i> +{{ $trend }}% this week
                    </span>
                @else
                    <span class="text-xs font-medium rounded-lg px-3 py-1 bg-red-100 text-red-700">
                        <i class="bi bi-graph-down-arrow"></i> {{ $trend }}% this week
                    </span>
                @endif
            </div>

            <div class="flex items-center gap-4 text-xs text-gray-600 mb-4">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-[var(--c-primary)]"></span> Borrowing</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-[#5FD3B0]"></span> Return</span>
            </div>

            <div class="flex items-end justify-between gap-2 bg-gray-50 rounded-xl px-3 pt-4 pb-2">
                @foreach ($days as $i => $day)
                    <div class="flex-1 flex flex-col items-center justify-end">
                        <div class="h-28 flex items-end gap-1">
                            <div class="w-3 rounded-t bg-[var(--c-primary)]"
                                 style="height: {{ $borrowData[$i] > 0 ? max(4, round($borrowData[$i] / $maxBar * 100)) : 0 }}%"
                                 title="Borrowing: {{ $borrowData[$i] }}"></div>
                            <div class="w-3 rounded-t bg-[#5FD3B0]"
                                 style="height: {{ $returnData[$i] > 0 ? max(4, round($returnData[$i] / $maxBar * 100)) : 0 }}%"
                                 title="Return: {{ $returnData[$i] }}"></div>
                        </div>
                        <span class="text-[11px] text-gray-500 mt-2">{{ $day }}</span>
                    </div>
                @endforeach
            </div>
        </x-card>

        {{-- alat paling sering dipinjam --}}
        <x-card class="xl:col-span-3">
            <h2 class="text-sm font-semibold mb-4">Top Borrowed Tools</h2>

            @forelse ($topTools as $i => $tool)
                <div class="flex items-center gap-4 {{ ! $loop->last ? 'mb-4' : '' }}">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-sm font-bold {{ $rankStyle[$i] ?? 'bg-gray-50 text-gray-500' }}">
                        {{ $i + 1 }}
                    </span>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline justify-between gap-3">
                            <div class="truncate text-sm font-medium">
                                {{ $tool['name'] }} <span class="text-xs font-normal text-gray-400">{{ $tool['code'] }}</span>
                            </div>
                            <span class="shrink-0 text-xs text-gray-600">{{ $tool['total'] }} borrowed</span>
                        </div>
                        <div class="mt-1.5 h-2 rounded-full bg-gray-100">
                            <div class="h-2 rounded-full bg-[var(--c-primary)]" style="width: {{ round($tool['total'] / $maxTop * 100) }}%"></div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="py-10 text-center text-sm text-gray-500">No borrowing data yet.</p>
            @endforelse
        </x-card>
    </div>
</x-app-layout>