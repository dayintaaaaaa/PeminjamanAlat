<x-app-layout>
    <x-slot name="header">User Management</x-slot>

    {{-- buat judul sama sub judul + button --}}
    <div class="flex items-start justify-between mb-8">
        <div>
            <h1 class="font-bold text-2xl">User Management</h1>
            <p class="text-sm text-gray-500 mt-1">Manage user data and new account registrations.</p>
        </div>
        <a href="{{ route('users.add-user') }}" class="bg-[var(--c-primary)] font-semibold text-sm text-white text-center rounded-lg px-6 py-2.5 shadow hover:opacity-90" title="Add Data">+ Add User</a>
    </div>

    {{-- deleteAction menyimpan alamat hapus dari baris yang diklik --}}
    <div x-data="{ deleteAction: '' }">

        <x-card>
            {{-- search + filter --}}
            <form id="filter-form" method="GET" action="{{ route('users.management-user') }}" class="flex gap-4 mb-6">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name..."
                    class="h-11 min-w-0 flex-1 rounded-lg px-6 shadow bg-[var(--c-accent)] text-white text-sm placeholder:text-white/90">

                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button type="button" @click="open = !open" title="Filter"
                        class="relative flex h-11 w-36 items-center justify-center gap-2 rounded-lg shadow bg-[var(--c-accent)] text-white font-medium text-sm hover:opacity-90">
                        <i class="bi bi-funnel"></i> Filter
                        @if (request('role'))
                        <span class="absolute -right-1 -top-1 h-3 w-3 rounded-full bg-red-500 ring-2 ring-white"></span>
                        @endif
                    </button>

                    <div x-show="open" x-cloak x-transition
                        class="absolute right-0 z-20 mt-2 w-56 rounded-lg bg-white p-4 shadow-lg ring-1 ring-black/5">
                        <label for="role" class="mb-1 block text-xs font-medium text-gray-500">Role</label>
                        <select id="role" name="role"
                            class="w-full rounded-md border-gray-300 text-sm focus:border-[var(--c-primary)] focus:ring-[var(--c-primary)]">
                            <option value="">All roles</option>
                            @foreach ($roles as $role)
                            <option value="{{ $role }}" @selected(request('role')===$role)>{{ ucfirst($role) }}</option>
                            @endforeach
                        </select>

                        <div class="mt-4 flex items-center justify-between">
                            <a href="{{ route('users.management-user') }}" class="text-xs text-gray-500 hover:text-gray-900">Reset</a>
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
                        <th class="py-3 w-16 text-center">No</th>
                        <th class="py-3 text-center">Name</th>
                        <th class="py-3 text-center">Role</th>
                        <th class="py-3 w-24 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $u)
                    <tr class="border-b border-gray-200 last:border-0">
                        <td class="py-3 text-center">{{ $users->firstItem() + $loop->index }}</td>
                        <td class="py-3">
                            <div class="font-medium leading-tight">{{ $u->name }}</div>
                            <div class="text-xs text-gray-400">{{ $u->email }}</div>
                        </td>
                        <td class="py-3 text-center">{{ ucfirst($u->getRoleNames()->first() ?? '-') }}</td>
                        <td class="py-3 text-center">
                            <div class="relative inline-block" x-data="{ open: false }" @click.outside="open = false">
                                <button type="button" @click="open = !open" class="px-2 text-gray-500 hover:text-gray-900">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <div x-show="open" x-cloak x-transition
                                    class="absolute right-0 z-20 mt-1 w-32 bg-white rounded-lg shadow-lg border border-gray-100 text-left text-sm py-1">
                                    <a href="{{ route('users.edit-user', $u) }}" class="block px-4 py-2 hover:bg-gray-50">
                                        <i class="bi bi-pencil mr-2"></i>Edit
                                    </a>

                                    {{-- akun sendiri tidak boleh dihapus --}}
                                    @if ($u->id !== auth()->id())
                                    <button type="button"
                                        @click="open = false; deleteAction = '{{ route('users.destroy', $u) }}'; $dispatch('open-modal', 'confirm-delete')"
                                        class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-50">
                                        <i class="bi bi-trash mr-2"></i>Delete
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-10 text-center text-gray-500">
                            No users found. Try a different name or clear the filter.
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
                        <option value="{{ $n }}" @selected($perPage===$n)>{{ $n }}</option>
                        @endforeach
                    </select>
                </label>

                <div>
                    {{ $users->links() }}
                </div>
            </div>
        </x-card>

        {{-- form hapus tersembunyi; action-nya diisi saat tombol Delete diklik --}}
        <form id="delete-form" method="POST" :action="deleteAction" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        {{-- alert konfirmasi hapus --}}
        <x-alert-modal name="confirm-delete" type="confirm" title="Delete data" message="Are you sure delete this data?">
            <button type="button" x-on:click="$dispatch('close-modal', 'confirm-delete')"
                class="rounded-md bg-gray-200 px-6 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-300">
                Cancel
            </button>
            <button type="submit" form="delete-form"
                class="rounded-md bg-red-600 px-6 py-1.5 text-xs font-semibold text-white hover:bg-red-700">
                Delete
            </button>
        </x-alert-modal>

    </div>
</x-app-layout>