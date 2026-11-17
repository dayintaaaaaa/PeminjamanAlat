<x-app-layout>
    <x-slot name="header">
        <span class="font-normal text-gray-500">Master Data</span>
        <span class="font-normal text-gray-500">/</span>
        Tools Data
    </x-slot>

    @php
    // class ditulis utuh supaya dikenali Tailwind
    $statusColor = [
    'Available' => 'text-green-600',
    'Borrowed' => 'text-red-600',
    'Maintenance' => 'text-orange-500',
    ];
    @endphp

    {{-- judul + tombol Add Tool --}}
    <div class="flex items-start justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold">Tools Data</h1>
            <p class="text-sm text-gray-500 mt-1">Manage and monitor inventory equipment data.</p>
        </div>
        @role('admin')
        <a href="{{ route('master.add-tool') }}"
            class="bg-[var(--c-primary)] text-white text-sm font-semibold rounded-lg px-5 py-3 shadow hover:opacity-90">
            + Add Tool
        </a>
        @endrole
    </div>

    <div x-data="{
            deleteAction: '',
            detail: { tool: '', category: '', condition: '', total: 0, available: 0, location: '', description: '', created: '', updated: '', photo: null }
         }">

        <x-card>
            {{-- search + filter --}}
            <form id="filter-form" method="GET" action="{{ route('master.tools-data') }}" class="flex gap-4 mb-6">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tools..."
                    class="h-11 min-w-0 flex-1 rounded-lg px-6 shadow bg-[var(--c-accent)] text-white text-sm placeholder:text-white/90">

                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button type="button" @click="open = !open" title="Filter"
                        class="relative flex h-11 w-36 items-center justify-center gap-2 rounded-lg shadow bg-[var(--c-accent)] text-white font-medium text-sm hover:opacity-90">
                        <i class="bi bi-funnel"></i> Filter
                        @if (request('status') || request('category'))
                        <span class="absolute -right-1 -top-1 h-3 w-3 rounded-full bg-red-500 ring-2 ring-white"></span>
                        @endif
                    </button>

                    <div x-show="open" x-cloak x-transition
                        class="absolute right-0 z-20 mt-2 w-60 space-y-3 rounded-lg bg-white p-4 shadow-lg ring-1 ring-black/5">
                        <div>
                            <label for="f_category" class="mb-1 block text-xs font-medium text-gray-500">Category</label>
                            <select id="f_category" name="category"
                                class="w-full rounded-md border-gray-300 text-sm focus:border-[var(--c-primary)] focus:ring-[var(--c-primary)]">
                                <option value="">All categories</option>
                                @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected((string) request('category')===(string) $cat->id)>{{ $cat->category_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="f_status" class="mb-1 block text-xs font-medium text-gray-500">Status</label>
                            <select id="f_status" name="status"
                                class="w-full rounded-md border-gray-300 text-sm focus:border-[var(--c-primary)] focus:ring-[var(--c-primary)]">
                                <option value="">All status</option>
                                @foreach (['Available', 'Borrowed', 'Maintenance'] as $s)
                                <option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center justify-between pt-1">
                            <a href="{{ route('master.tools-data') }}" class="text-xs text-gray-500 hover:text-gray-900">Reset</a>
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
                        <th class="py-3 text-center">Tool Code</th>
                        <th class="py-3 text-center">Tool Name</th>
                        <th class="py-3 text-center">Total Qty</th>
                        <th class="py-3 text-center">Available</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3 w-20 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tools as $t)
                    <tr class="border-b border-gray-200 last:border-0">
                        <td class="py-3 text-center">{{ $tools->firstItem() + $loop->index }}</td>
                        <td class="py-3 text-center">{{ $t->tools_code }}</td>
                        <td class="py-3 text-center">{{ $t->tools_name }}</td>
                        <td class="py-3 text-center">{{ $t->tools_qty }} Items</td>
                        <td class="py-3 text-center">{{ $t->available }} Items</td>
                        <td class="py-3 text-center font-semibold {{ $statusColor[$t->status] ?? 'text-gray-500' }}">{{ $t->status }}</td>
                        <td class="py-3 text-center">
                            <div class="relative inline-block" x-data="{ open: false }" @click.outside="open = false">
                                <button type="button" @click="open = !open" class="px-2 text-gray-500 hover:text-gray-900">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <div x-show="open" x-cloak x-transition
                                    class="absolute right-0 z-20 mt-1 w-32 bg-white rounded-lg shadow-lg border border-gray-100 text-left text-sm py-1">

                                    @role('admin')
                                        <a href="{{ route('master.edit-tool', $t) }}" class="block px-4 py-2 hover:bg-gray-50">
                                            <i class="bi bi-pencil mr-2"></i>Edit
                                        </a>

                                        <button type="button"
                                            @click="open = false;
                                                    deleteAction = '{{ route('tools.destroy', $t) }}';
                                                    $dispatch('open-modal', 'confirm-delete')"
                                                class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-50">
                                            <i class="bi bi-trash mr-2"></i>Delete
                                        </button>
                                    @endrole

                                    <button type="button"
                                        @click="open = false;
                                                        detail = @js([
                                                            'tool'        => $t->tools_code . ' | ' . $t->tools_name,
                                                            'category'    => ($t->category->category_code ?? '-') . ' | ' . ($t->category->category_name ?? '-'),
                                                            'condition'   => $t->tools_condition ?? '-',
                                                            'total'       => $t->tools_qty,
                                                            'available'   => $t->available,
                                                            'location'    => $t->storage_location ?: '-',
                                                            'description' => $t->description ?: '-',
                                                            'created'     => $t->created_at?->format('d M Y, H:i'),
                                                            'updated'     => $t->updated_at?->format('d M Y, H:i'),
                                                            'photo'       => $t->photo ? asset('storage/' . $t->photo) : null,
                                                        ]);
                                                        $dispatch('open-modal', 'detail-tool')"
                                        class="block w-full text-left px-4 py-2 hover:bg-gray-50">
                                        <i class="bi bi-info-circle mr-2"></i>Detail
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center text-gray-500">
                            No tools found. Try a different name or add a new tool.
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

                <div>{{ $tools->links() }}</div>
            </div>
        </x-card>

        {{-- form hapus tersembunyi --}}
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

        {{-- modal detail (di dalam wrapper supaya bisa membaca state "detail") --}}
        @include('master.detail-tools')
    </div>
</x-app-layout>