<x-app-layout>
    <x-slot name="header">User Management</x-slot>

    @php
        // data contoh, nanti diganti data dari database
        $users = [
            ['name' => 'Martin J',        'email' => 'martinje@gmail.com',  'role' => 'Admin'],
            ['name' => 'James Januar',    'email' => 'jamars@gmail.com',    'role' => 'Petugas'],
            ['name' => 'David Adrison S', 'email' => 'daviddris@gmail.com', 'role' => 'Peminjam'],
            ['name' => 'Lee Jeno',        'email' => 'jenlee@gmail.com',    'role' => 'Peminjam'],
        ];
    @endphp

    {{-- judul + tombol Add User --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">User Management</h1>
            <p class="text-sm text-gray-500 mt-1">Manage user data and new account registrations.</p>
        </div>
        <a href="#" class="bg-[var(--c-primary)] text-white text-sm font-semibold rounded-lg px-5 py-3 shadow hover:opacity-90">
            + Add User
        </a>
    </div>

    <x-card>
        {{-- search + filter --}}
        <div class="flex gap-4 mb-5">
            <input type="text" placeholder="Search name..." class="flex-1 rounded-lg border-0 bg-[var(--c-accent)] text-white placeholder:text-white/90 text-sm focus:ring-2 focus:ring-[var(--c-primary)]">
            <button type="button"
                    class="flex items-center gap-2 rounded-lg bg-[var(--c-accent)] text-white text-sm font-medium px-6 hover:opacity-90">
                <i class="bi bi-funnel"></i> Filter
            </button>
        </div>

        {{-- tabel --}}
        <table class="w-full text-sm">
            <thead>
                <tr class="font-semibold text-gray-900 border-b border-gray-200">
                    <th class="py-3 w-16 text-center">No</th>
                    <th class="py-3 text-center">Name</th>
                    <th class="py-3 text-center">Role</th>
                    <th class="py-3 w-24 text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $i => $user)
                    <tr class="border-b border-gray-200 last:border-0">
                        <td class="py-3 text-center">{{ $i + 1 }}</td>
                        <td class="py-3">
                            <div class="font-medium leading-tight">{{ $user['name'] }}</div>
                            <div class="text-[11px] text-gray-500">{{ $user['email'] }}</div>
                        </td>
                        <td class="py-3 text-center">{{ $user['role'] }}</td>
                        <td class="py-3 text-center">
                            <div class="relative inline-block" x-data="{ open: false }" @click.outside="open = false">
                                <button type="button" @click="open = !open" class="px-2 text-gray-500 hover:text-gray-900">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <div x-show="open" x-cloak x-transition
                                     class="absolute right-0 z-20 mt-1 w-32 bg-white rounded-lg shadow-lg border border-gray-100 text-left text-sm py-1">
                                    <a href="#" class="block px-4 py-2 hover:bg-gray-50">
                                        <i class="bi bi-pencil mr-2"></i>Edit
                                    </a>
                                    <button type="button" class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-50">
                                        <i class="bi bi-trash mr-2"></i>Delete
                                    </button>
                                </div>
                            </div>
                        </td>
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