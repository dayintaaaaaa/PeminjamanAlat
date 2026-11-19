<x-app-layout>
    <x-slot name="header">Activity Log</x-slot>

    @php
        // class ditulis utuh supaya dikenali Tailwind
        $actionColor = [
            'Login'   => 'text-gray-500',
            'Logout'  => 'text-gray-500',
            'Create'  => 'text-green-600',
            'Update'  => 'text-orange-500',
            'Delete'  => 'text-red-600',
            'Approve' => 'text-green-600',
            'Reject'  => 'text-red-600',
        ];
    @endphp

    {{-- judul --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold">Activity Log</h1>
        <p class="text-sm text-gray-500 mt-1">Monitor system audit trails and user activity history.</p>
    </div>

    <x-card>
        {{-- search + export + filter --}}
        <form id="filter-form" method="GET" action="{{ route('activity-log.activity-log') }}" class="flex gap-4 mb-6">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                   class="h-11 min-w-0 flex-1 rounded-lg px-6 shadow bg-[var(--c-accent)] text-white text-sm placeholder:text-white/90">

            <a href="{{ route('activity-log.export', request()->only(['search', 'action', 'module', 'date_from', 'date_to'])) }}"
               title="Export to Excel"
               class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-[var(--c-accent)] text-white hover:opacity-90">
                <i class="bi bi-file-earmark-excel"></i>
            </a>

            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button type="button" @click="open = !open" title="Filter"
                        class="relative flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-[var(--c-accent)] text-white hover:opacity-90">
                    <i class="bi bi-funnel"></i>
                    @if (request('action') || request('module') || request('date_from') || request('date_to'))
                        <span class="absolute -right-1 -top-1 h-3 w-3 rounded-full bg-red-500 ring-2 ring-white"></span>
                    @endif
                </button>

                <div x-show="open" x-cloak x-transition
                     class="absolute right-0 z-20 mt-2 w-64 space-y-3 rounded-lg bg-white p-4 shadow-lg ring-1 ring-black/5">
                    <div>
                        <label for="f_action" class="mb-1 block text-xs font-medium text-gray-500">Action</label>
                        <select id="f_action" name="action"
                                class="w-full rounded-md border-gray-300 text-sm focus:border-[var(--c-primary)] focus:ring-[var(--c-primary)]">
                            <option value="">All actions</option>
                            @foreach ($actions as $a)
                                <option value="{{ $a }}" @selected(request('action') === $a)>{{ $a }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="f_module" class="mb-1 block text-xs font-medium text-gray-500">Module</label>
                        <select id="f_module" name="module"
                                class="w-full rounded-md border-gray-300 text-sm focus:border-[var(--c-primary)] focus:ring-[var(--c-primary)]">
                            <option value="">All modules</option>
                            @foreach ($modules as $m)
                                <option value="{{ $m }}" @selected(request('module') === $m)>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label for="f_from" class="mb-1 block text-xs font-medium text-gray-500">From</label>
                            <input type="date" id="f_from" name="date_from" value="{{ request('date_from') }}"
                                   class="w-full rounded-md border-gray-300 text-xs focus:border-[var(--c-primary)] focus:ring-[var(--c-primary)]">
                        </div>
                        <div>
                            <label for="f_to" class="mb-1 block text-xs font-medium text-gray-500">To</label>
                            <input type="date" id="f_to" name="date_to" value="{{ request('date_to') }}"
                                   class="w-full rounded-md border-gray-300 text-xs focus:border-[var(--c-primary)] focus:ring-[var(--c-primary)]">
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-1">
                        <a href="{{ route('activity-log.activity-log') }}" class="text-xs text-gray-500 hover:text-gray-900">Reset</a>
                        <button type="submit"
                                class="rounded-md bg-[var(--c-primary)] px-4 py-1.5 text-xs font-semibold text-white hover:opacity-90">
                            Apply
                        </button>
                    </div>
                </div>
            </div>
        </form>

        {{-- tabel --}}
        <table class="w-full text-sm">
            <thead>
                <tr class="font-semibold text-gray-900 border-b border-gray-200">
                    <th class="py-3 w-12 text-center">No</th>
                    <th class="py-3 text-center">Timestamp</th>
                    <th class="py-3 text-center">Admin/User</th>
                    <th class="py-3 text-center">Action</th>
                    <th class="py-3 text-center">Module</th>
                    <th class="py-3 text-center">Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr class="border-b border-gray-200 last:border-0">
                        <td class="py-3 text-center">{{ $logs->firstItem() + $loop->index }}</td>
                        <td class="py-3 text-center">{{ $log->created_at?->format('d/m/Y - H:i') }}</td>
                        <td class="py-3 text-center">{{ $log->user_name ?? '-' }}</td>
                        <td class="py-3 text-center font-semibold {{ $actionColor[$log->action] ?? 'text-gray-500' }}">{{ $log->action }}</td>
                        <td class="py-3 text-center">{{ $log->module }}</td>
                        <td class="py-3 text-center">{{ $log->description }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-gray-500">
                            No activity found. Try a different keyword or clear the filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- rows per page + pagination --}}
        <div class="flex items-center justify-between mt-5 text-xs text-gray-600">
            <label class="flex items-center gap-2">
                Rows per page
                <select name="per_page" form="filter-form" onchange="this.form.submit()"
                        class="rounded-md border-0 bg-[var(--c-accent)] text-white text-xs py-1 pl-3 pr-7">
                    @foreach ([5, 10, 25] as $n)
                        <option value="{{ $n }}" @selected($perPage === $n)>{{ $n }}</option>
                    @endforeach
                </select>
            </label>

            <div>{{ $logs->links() }}</div>
        </div>
    </x-card>
</x-app-layout>