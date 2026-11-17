<x-app-layout>
    <x-slot name="header">
        <span class="font-normal text-gray-500">Master Data</span>
        <span class="font-normal text-gray-500">/</span>
        Tools Category
    </x-slot>

    {{-- judul + tombol Add Category --}}
    <div class="flex items-start justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold">Tools Category</h1>
            <p class="text-sm text-gray-500 mt-1">Manage tool categories and equipment classifications.</p>
        </div>
        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-category')"
                class="bg-[var(--c-primary)] text-white text-sm font-semibold rounded-lg px-5 py-3 shadow hover:opacity-90">
            + Add Category
        </button>
    </div>

    {{-- state halaman: dipakai bersama oleh modal Edit, Detail, dan Delete --}}
    <div x-data="{
            deleteAction: '',
            editAction: @js(old('_edit_action', '')),
            edit: { code: @js(old('code', '')), name: @js(old('name', '')), description: @js(old('description', '')) },
            detail: { code: '', name: '', total: 0, description: '' }
         }">

        <x-card>
            {{-- search --}}
            <form id="filter-form" method="GET" action="{{ route('master.tools-category') }}" class="mb-6">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search category..."
                       class="h-11 w-full rounded-lg px-6 shadow bg-[var(--c-accent)] text-white text-sm placeholder:text-white/90">
            </form>

            {{-- tabel --}}
            <table class="w-full text-sm">
                <thead>
                    <tr class="font-semibold text-gray-900 border-b border-gray-200">
                        <th class="py-3 w-16 text-center">No</th>
                        <th class="py-3 text-left">Category Name</th>
                        <th class="py-3 text-center">Total Items</th>
                        <th class="py-3 w-24 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $c)
                        <tr class="border-b border-gray-200 last:border-0">
                            <td class="py-3 text-center">{{ $categories->firstItem() + $loop->index }}</td>
                            <td class="py-3">{{ $c->category_name }}</td>
                            <td class="py-3 text-center">{{ $c->tools_sum_tools_qty ?? 0 }} Items</td>
                            <td class="py-3 text-center">
                                <div class="relative inline-block" x-data="{ open: false }" @click.outside="open = false">
                                    <button type="button" @click="open = !open" class="px-2 text-gray-500 hover:text-gray-900">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <div x-show="open" x-cloak x-transition
                                         class="absolute right-0 z-20 mt-1 w-32 bg-white rounded-lg shadow-lg border border-gray-100 text-left text-sm py-1">

                                        {{-- Edit: isi form modal dengan data baris ini --}}
                                        <button type="button"
                                                @click="open = false;
                                                        editAction = '{{ route('tool-categories.update', $c) }}';
                                                        edit = @js(['code' => $c->category_code, 'name' => $c->category_name, 'description' => $c->description]);
                                                        $dispatch('open-modal', 'edit-category')"
                                                class="block w-full text-left px-4 py-2 hover:bg-gray-50">
                                            <i class="bi bi-pencil mr-2"></i>Edit
                                        </button>

                                        {{-- Delete: simpan alamat hapus baris ini --}}
                                        <button type="button"
                                                @click="open = false;
                                                        deleteAction = '{{ route('tool-categories.destroy', $c) }}';
                                                        $dispatch('open-modal', 'confirm-delete')"
                                                class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-50">
                                            <i class="bi bi-trash mr-2"></i>Delete
                                        </button>

                                        {{-- Detail --}}
                                        <button type="button"
                                                @click="open = false;
                                                        detail = @js(['code' => $c->category_code, 'name' => $c->category_name, 'total' => (int) ($c->tools_sum_qty ?? 0), 'description' => $c->description ?: '-']);
                                                        $dispatch('open-modal', 'detail-category')"
                                                class="block w-full text-left px-4 py-2 hover:bg-gray-50">
                                            <i class="bi bi-info-circle mr-2"></i>Detail
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-gray-500">
                                No categories found. Try a different name or add a new category.
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

                <div>{{ $categories->links() }}</div>
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

        {{-- tiga modal (di dalam wrapper supaya bisa membaca state di atas) --}}
        @include('master.add-category')
        @include('master.edit-category')
        @include('master.detail-category')
    </div>
</x-app-layout>