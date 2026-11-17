<x-app-layout>
    <x-slot name="header">My Borrowed</x-slot>

    @php
        // class ditulis utuh supaya dikenali Tailwind
        $statusColor = [
            'Pending'        => 'text-orange-500',
            'Active'         => 'text-green-600',
            'Overdue'        => 'text-red-600',
            'Pending Return' => 'text-orange-500',
            'Rejected'       => 'text-gray-500',
            'Returned'       => 'text-gray-500',
        ];
    @endphp

    {{-- judul + tombol Add Request --}}
    <div class="flex items-start justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold">My Borrowed</h1>
            <p class="text-sm text-gray-500 mt-1">Manage your active borrowings, submit new requests, and request tool returns.</p>
        </div>
        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-request')"
                class="bg-[var(--c-primary)] text-white text-sm font-semibold rounded-lg px-5 py-3 shadow hover:opacity-90">
            + Add Request
        </button>
    </div>

    <div x-data="{
            returnAction: '',
            detail: { borrower: '', status: '', statusClass: '', code: '', created: '', tool: '', category: '', qty: 0, location: '', photo: null, borrowDate: '', dueDate: '', processedBy: '', note: '' }
         }">

        <x-card>
            {{-- search + filter --}}
            <form id="filter-form" method="GET" action="{{ route('my-borrowed.my-borrowed') }}" class="flex gap-4 mb-6">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                       class="h-11 min-w-0 flex-1 rounded-lg px-6 shadow bg-[var(--c-accent)] text-white text-sm placeholder:text-white/90">

                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button type="button" @click="open = !open" title="Filter"
                            class="relative flex h-11 w-36 items-center justify-center gap-2 rounded-lg shadow bg-[var(--c-accent)] text-white font-medium text-sm hover:opacity-90">
                        <i class="bi bi-funnel"></i> Filter
                        @if (request('status'))
                            <span class="absolute -right-1 -top-1 h-3 w-3 rounded-full bg-red-500 ring-2 ring-white"></span>
                        @endif
                    </button>

                    <div x-show="open" x-cloak x-transition
                         class="absolute right-0 z-20 mt-2 w-56 rounded-lg bg-white p-4 shadow-lg ring-1 ring-black/5">
                        <label for="f_status" class="mb-1 block text-xs font-medium text-gray-500">Status</label>
                        <select id="f_status" name="status"
                                class="w-full rounded-md border-gray-300 text-sm focus:border-[var(--c-primary)] focus:ring-[var(--c-primary)]">
                            <option value="">All status</option>
                            @foreach (['Pending', 'Active', 'Overdue', 'Pending Return', 'Rejected', 'Returned'] as $s)
                                <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
                            @endforeach
                        </select>

                        <div class="mt-4 flex items-center justify-between">
                            <a href="{{ route('my-borrowed.my-borrowed') }}" class="text-xs text-gray-500 hover:text-gray-900">Reset</a>
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
                        <th class="py-3 text-center">Borrow ID</th>
                        <th class="py-3 text-center">Tool Name</th>
                        <th class="py-3 text-center">Borrow Date</th>
                        <th class="py-3 text-center">Due Date</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3 w-20 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($borrowings as $b)
                        @php $st = $b->display_status; @endphp
                        <tr class="border-b border-gray-200 last:border-0">
                            <td class="py-3 text-center">{{ $borrowings->firstItem() + $loop->index }}</td>
                            <td class="py-3 text-center">{{ $b->borrow_code }}</td>
                            <td class="py-3 text-center">{{ $b->tool->tools_name ?? '-' }}</td>
                            <td class="py-3 text-center">{{ $b->borrow_date?->format('j/n/y') }}</td>
                            <td class="py-3 text-center">{{ $b->due_date?->format('j/n/y') }}</td>
                            <td class="py-3 text-center font-semibold {{ $statusColor[$st] ?? 'text-gray-500' }}">{{ $st }}</td>
                            <td class="py-3 text-center">
                                <div class="relative inline-block" x-data="{ open: false }" @click.outside="open = false">
                                    <button type="button" @click="open = !open" class="px-2 text-gray-500 hover:text-gray-900">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <div x-show="open" x-cloak x-transition
                                         class="absolute right-0 z-20 mt-1 w-32 bg-white rounded-lg shadow-lg border border-gray-100 text-left text-sm py-1">

                                        {{-- Return hanya untuk yang Active atau Overdue dan belum diajukan --}}
                                        @if ($st === 'Active' || $st === 'Overdue')
                                            <button type="button"
                                                    @click="open = false;
                                                            returnAction = '{{ route('my-borrowed.return', $b) }}';
                                                            $dispatch('open-modal', 'confirm-return')"
                                                    class="block w-full text-left px-4 py-2 hover:bg-gray-50">
                                                <i class="bi bi-arrow-return-left mr-2 text-[var(--c-primary)]"></i>Return
                                            </button>
                                        @endif

                                        <button type="button"
                                                @click="open = false;
                                                        detail = @js([
                                                            'borrower'    => auth()->user()->name,
                                                            'status'      => $st,
                                                            'statusClass' => $statusColor[$st] ?? 'text-gray-500',
                                                            'code'        => $b->borrow_code,
                                                            'created'     => $b->created_at?->format('d-m-Y'),
                                                            'tool'        => ($b->tool->tools_code ?? '-') . ' | ' . ($b->tool->tools_name ?? '-'),
                                                            'category'    => ($b->tool?->category?->category_code ?? '-') . ' | ' . ($b->tool?->category?->category_name ?? '-'),
                                                            'qty'         => $b->borrow_qty,
                                                            'location'    => $b->tool?->storage_location ?: '-',
                                                            'photo'       => $b->tool?->photo ? asset('storage/' . $b->tool->photo) : null,
                                                            'borrowDate'  => $b->borrow_date?->format('d-m-Y'),
                                                            'dueDate'     => $b->due_date?->format('d-m-Y'),
                                                            'processedBy' => $b->officer?->name ?? '-',
                                                            'note'        => $b->notes ?: '-',
                                                        ]);
                                                        $dispatch('open-modal', 'detail-borrowed')"
                                                class="block w-full text-left px-4 py-2 hover:bg-gray-50">
                                            <i class="bi bi-info-circle mr-2 text-[var(--c-primary)]"></i>Detail
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-gray-500">
                                No borrowing records yet. Click "+ Add Request" to borrow a tool.
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

                <div>{{ $borrowings->links() }}</div>
            </div>
        </x-card>

        {{-- form tersembunyi; action-nya diisi saat Return diklik --}}
        <form id="return-form" method="POST" :action="returnAction" class="hidden">
            @csrf
        </form>

        {{-- konfirmasi return --}}
        <x-alert-modal name="confirm-return" type="return" title="Confirm Return"
                       message="Return this tool? Status will update to Pending Return upon staff verification.">
            <button type="button" x-on:click="$dispatch('close-modal', 'confirm-return')"
                    class="rounded-md bg-gray-200 px-6 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-300">
                Cancel
            </button>
            <button type="submit" form="return-form"
                    class="rounded-md bg-red-600 px-6 py-1.5 text-xs font-semibold text-white hover:bg-red-700">
                Yes
            </button>
        </x-alert-modal>

        @include('my-borrowed.add-request')
        @include('borrowed.detail-borrowed')
    </div>
</x-app-layout>