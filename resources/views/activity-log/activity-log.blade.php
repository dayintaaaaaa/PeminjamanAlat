<x-app-layout>
    <x-slot name="header">Activity Log</x-slot>

    @php
        // data contoh, nanti diganti data dari database
        $logs = [
            ['time' => '02/10/2026 - 14:15', 'user' => 'David Adrison S', 'action' => 'Login',  'module' => 'Auth',                'desc' => 'User logged'],
            ['time' => '02/10/2026 - 14:15', 'user' => 'Martin J',        'action' => 'Create', 'module' => 'Transaction - Borrow', 'desc' => 'Created borrowing'],
        ];

        // class ditulis utuh supaya dikenali Tailwind
        $actionColor = [
            'Login'  => 'text-gray-500',
            'Create' => 'text-green-600',
            'Update' => 'text-orange-500',
            'Delete' => 'text-red-600',
        ];
    @endphp

    {{-- judul --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold">Activity Log</h1>
        <p class="text-sm text-gray-500 mt-1">Monitor system audit trails and user activity history.</p>
    </div>

    <x-card>
        {{-- search + 2 tombol ikon --}}
        <div class="flex gap-4 mb-6">
            <input type="text" placeholder="Search tools..."
                   class="h-11 min-w-0 flex-1 rounded-lg px-6 shadow bg-[var(--c-accent)] text-white text-sm placeholder:text-white/90">

            <button type="button" title="Export"
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-[var(--c-accent)] text-white hover:opacity-90">
                <i class="bi bi-file-earmark-text"></i>
            </button>
            <button type="button" title="Filter"
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-[var(--c-accent)] text-white hover:opacity-90">
                <i class="bi bi-funnel"></i>
            </button>
        </div>

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
                @foreach ($logs as $i => $log)
                    <tr class="border-b border-gray-200 last:border-0">
                        <td class="py-3 text-center">{{ $i + 1 }}</td>
                        <td class="py-3 text-center">{{ $log['time'] }}</td>
                        <td class="py-3 text-center">{{ $log['user'] }}</td>
                        <td class="py-3 text-center font-semibold {{ $actionColor[$log['action']] }}">{{ $log['action'] }}</td>
                        <td class="py-3 text-center">{{ $log['module'] }}</td>
                        <td class="py-3 text-center">{{ $log['desc'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- rows per page + pagination --}}
        <div class="flex items-center justify-between mt-5 text-xs text-gray-600">
            <label class="flex items-center gap-2">
                Rows per page
                <select class="rounded-md border-0 bg-[var(--c-accent)] text-white text-xs py-1 pl-3 pr-7">
                    <option>5</option>
                    <option>10</option>
                    <option>25</option>
                </select>
            </label>

            <div class="flex items-center gap-3">
                <button type="button" class="text-gray-700"><i class="bi bi-chevron-left"></i></button>
                <span class="w-6 h-6 rounded-md bg-[var(--c-accent)] text-white font-semibold flex items-center justify-center">1</span>
                <button type="button" class="text-gray-700"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
    </x-card>
</x-app-layout>