<x-app-layout>
    <x-slot name="header">Transaction</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold">Transaction</h1>
        <p class="text-sm text-gray-500 mt-1">Manage and monitor equipment borrowing and return records.</p>
    </div>

    <div x-data="{
            tab: @js($tab),
            setTab(t) {
                this.tab = t;
                const url = new URL(window.location);
                url.searchParams.set('tab', t);
                history.replaceState(null, '', url);
            },
            deleteAction: '',
            editBorrowAction: '',
            editBorrow: { code: '', borrowDate: '', dueDate: '', id_user: '', id_tools: '', qty: '', notes: '' },
            editReturnAction: '',
            editReturn: { code: '', borrower: '', tool: '', borrowDate: '', dueDate: '', dueIso: '', returnDate: '', condition: 'Good', notes: '' },
            get editReturnStatus() {
                if (!this.editReturn.returnDate || !this.editReturn.dueIso) return '';
                const late = Math.round((new Date(this.editReturn.returnDate) - new Date(this.editReturn.dueIso)) / 86400000);
                return late > 0 ? 'Late (' + late + ' Days)' : 'On Time (0 Days)';
            }
         }">

        <x-card>
            {{-- tab --}}
            <div class="flex border-b border-gray-200 mb-5 text-sm">
                <button type="button" @click="setTab('borrowing')"
                        :class="tab === 'borrowing' ? 'border-[var(--c-primary)] font-semibold text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-900'"
                        class="px-4 py-2 border-b-2 -mb-px">
                    Borrowing
                </button>
                <button type="button" @click="setTab('return')"
                        :class="tab === 'return' ? 'border-[var(--c-primary)] font-semibold text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-900'"
                        class="px-4 py-2 border-b-2 -mb-px">
                    Return
                </button>
            </div>

            {{-- ===================== TAB BORROWING ===================== --}}
            <div x-show="tab === 'borrowing'">

                <form id="borrow-filter" method="GET" action="{{ route('transactions.transaction') }}" class="flex gap-4 mb-6">
                    <input type="hidden" name="tab" value="borrowing">

                    <input type="text" name="b_search" value="{{ request('b_search') }}" placeholder="Search..."
                           class="h-11 min-w-0 flex-1 rounded-lg px-6 shadow bg-[var(--c-accent)] text-white text-sm placeholder:text-white/90">

                    <a href="{{ route('transactions.export', ['type' => 'borrowing'] + request()->only(['b_search', 'b_status'])) }}"
                       title="Export to Excel"
                       class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-[var(--c-accent)] text-white hover:opacity-90">
                        <i class="bi bi-file-earmark-excel"></i>
                    </a>

                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button" @click="open = !open" title="Filter"
                                class="relative flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-[var(--c-accent)] text-white hover:opacity-90">
                            <i class="bi bi-funnel"></i>
                            @if (request('b_status'))
                                <span class="absolute -right-1 -top-1 h-3 w-3 rounded-full bg-red-500 ring-2 ring-white"></span>
                            @endif
                        </button>

                        <div x-show="open" x-cloak x-transition
                             class="absolute right-0 z-20 mt-2 w-56 rounded-lg bg-white p-4 shadow-lg ring-1 ring-black/5">
                            <label for="b_status" class="mb-1 block text-xs font-medium text-gray-500">Status</label>
                            <select id="b_status" name="b_status"
                                    class="w-full rounded-md border-gray-300 text-sm focus:border-[var(--c-primary)] focus:ring-[var(--c-primary)]">
                                <option value="">All status</option>
                                @foreach (['Pending', 'Active', 'Overdue', 'Pending Return', 'Return Rejected', 'Rejected', 'Completed'] as $s)
                                    <option value="{{ $s }}" @selected(request('b_status') === $s)>{{ $s }}</option>
                                @endforeach
                            </select>
                            <div class="mt-4 flex items-center justify-between">
                                <a href="{{ route('transactions.transaction', ['tab' => 'borrowing']) }}" class="text-xs text-gray-500 hover:text-gray-900">Reset</a>
                                <button type="submit" class="rounded-md bg-[var(--c-primary)] px-4 py-1.5 text-xs font-semibold text-white hover:opacity-90">Apply</button>
                            </div>
                        </div>
                    </div>

                    <button type="button" title="Add borrowing" @click="$dispatch('open-modal', 'add-borrowing')"
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-[var(--c-accent)] text-white hover:opacity-90">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </form>

                <table class="w-full text-sm">
                    <thead>
                        <tr class="font-semibold text-gray-900 border-b border-gray-200">
                            <th class="py-3 w-12 text-center">No</th>
                            <th class="py-3 text-center">Borrow ID</th>
                            <th class="py-3 text-center">Borrower</th>
                            <th class="py-3 text-center">Tool Name</th>
                            <th class="py-3 text-center">Borrow Date</th>
                            <th class="py-3 text-center">Due Date</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 w-20 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($borrowings as $b)
                            <tr class="border-b border-gray-200 last:border-0">
                                <td class="py-3 text-center">{{ $borrowings->firstItem() + $loop->index }}</td>
                                <td class="py-3 text-center">{{ $b->borrow_code }}</td>
                                <td class="py-3 text-center">{{ $b->borrower?->name ?? '-' }}</td>
                                <td class="py-3 text-center">{{ $b->tool?->tools_name ?? '-' }}</td>
                                <td class="py-3 text-center">{{ $b->borrow_date?->format('j/n/y') }}</td>
                                <td class="py-3 text-center">{{ $b->due_date?->format('j/n/y') }}</td>
                                <td class="py-3 text-center font-semibold {{ $b->statusClass() }}">{{ $b->display_status }}</td>
                                <td class="py-3 text-center">
                                    <div class="relative inline-block" x-data="{ open: false }" @click.outside="open = false">
                                        <button type="button" @click="open = !open" class="px-2 text-gray-500 hover:text-gray-900">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <div x-show="open" x-cloak x-transition
                                             class="absolute right-0 z-20 mt-1 w-32 bg-white rounded-lg shadow-lg border border-gray-100 text-left text-sm py-1">

                                            {{-- Edit hanya untuk Pending atau Active yang belum ada pengajuan return --}}
                                            @if (in_array($b->status, ['Pending', 'Active']) && ! $b->pendingReturn)
                                                <button type="button"
                                                        @click="open = false;
                                                                editBorrowAction = '{{ route('transactions.borrowings.update', $b) }}';
                                                                editBorrow = @js([
                                                                    'code'       => $b->borrow_code,
                                                                    'borrowDate' => $b->borrow_date?->format('d/m/Y'),
                                                                    'dueDate'    => $b->due_date?->format('d/m/Y'),
                                                                    'id_user'    => (string) $b->id_user,
                                                                    'id_tools'   => (string) $b->id_tools,
                                                                    'qty'        => $b->borrow_qty,
                                                                    'notes'      => $b->notes,
                                                                ]);
                                                                $dispatch('open-modal', 'edit-borrowing')"
                                                        class="block w-full text-left px-4 py-2 hover:bg-gray-50">
                                                    <i class="bi bi-pencil mr-2 text-[var(--c-primary)]"></i>Edit
                                                </button>
                                            @endif

                                            <button type="button"
                                                    @click="open = false;
                                                            deleteAction = '{{ route('transactions.borrowings.destroy', $b) }}';
                                                            $dispatch('open-modal', 'confirm-delete')"
                                                    class="block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-50">
                                                <i class="bi bi-trash mr-2"></i>Delete
                                            </button>

                                            <button type="button"
                                                    @click="open = false; $dispatch('show-borrow-detail', @js($b->detailData()))"
                                                    class="block w-full text-left px-4 py-2 hover:bg-gray-50">
                                                <i class="bi bi-info-circle mr-2 text-[var(--c-primary)]"></i>Detail
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-10 text-center text-gray-500">
                                    No borrowing records found. Try a different keyword or clear the filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="flex items-center justify-between mt-5 text-xs text-gray-600">
                    <label class="flex items-center gap-2">
                        Rows per page
                        <select name="b_per_page" form="borrow-filter" onchange="this.form.submit()"
                                class="rounded-md border-0 bg-[var(--c-accent)] text-white text-xs py-1 pl-3 pr-7">
                            @foreach ([5, 10, 25] as $n)
                                <option value="{{ $n }}" @selected($bPer === $n)>{{ $n }}</option>
                            @endforeach
                        </select>
                    </label>
                    <div>{{ $borrowings->links() }}</div>
                </div>
            </div>

            {{-- ======================= TAB RETURN ======================= --}}
            <div x-show="tab === 'return'" x-cloak>

                <form id="return-filter" method="GET" action="{{ route('transactions.transaction') }}" class="flex gap-4 mb-6">
                    <input type="hidden" name="tab" value="return">

                    <input type="text" name="r_search" value="{{ request('r_search') }}" placeholder="Search..."
                           class="h-11 min-w-0 flex-1 rounded-lg px-6 shadow bg-[var(--c-accent)] text-white text-sm placeholder:text-white/90">

                    <a href="{{ route('transactions.export', ['type' => 'return'] + request()->only(['r_search', 'r_condition', 'r_status'])) }}"
                       title="Export to Excel"
                       class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-[var(--c-accent)] text-white hover:opacity-90">
                        <i class="bi bi-file-earmark-excel"></i>
                    </a>

                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button" @click="open = !open" title="Filter"
                                class="relative flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-[var(--c-accent)] text-white hover:opacity-90">
                            <i class="bi bi-funnel"></i>
                            @if (request('r_condition') || request('r_status'))
                                <span class="absolute -right-1 -top-1 h-3 w-3 rounded-full bg-red-500 ring-2 ring-white"></span>
                            @endif
                        </button>

                        <div x-show="open" x-cloak x-transition
                             class="absolute right-0 z-20 mt-2 w-56 space-y-3 rounded-lg bg-white p-4 shadow-lg ring-1 ring-black/5">
                            <div>
                                <label for="r_condition" class="mb-1 block text-xs font-medium text-gray-500">Condition</label>
                                <select id="r_condition" name="r_condition"
                                        class="w-full rounded-md border-gray-300 text-sm focus:border-[var(--c-primary)] focus:ring-[var(--c-primary)]">
                                    <option value="">All conditions</option>
                                    @foreach (['Good', 'Damaged', 'Lost'] as $c)
                                        <option value="{{ $c }}" @selected(request('r_condition') === $c)>{{ $c }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="r_status" class="mb-1 block text-xs font-medium text-gray-500">Status</label>
                                <select id="r_status" name="r_status"
                                        class="w-full rounded-md border-gray-300 text-sm focus:border-[var(--c-primary)] focus:ring-[var(--c-primary)]">
                                    <option value="">All status</option>
                                    @foreach (['Pending', 'Approved', 'Rejected'] as $s)
                                        <option value="{{ $s }}" @selected(request('r_status') === $s)>{{ $s }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-center justify-between pt-1">
                                <a href="{{ route('transactions.transaction', ['tab' => 'return']) }}" class="text-xs text-gray-500 hover:text-gray-900">Reset</a>
                                <button type="submit" class="rounded-md bg-[var(--c-primary)] px-4 py-1.5 text-xs font-semibold text-white hover:opacity-90">Apply</button>
                            </div>
                        </div>
                    </div>

                    <button type="button" title="Add return" @click="$dispatch('open-modal', 'add-return')"
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-[var(--c-accent)] text-white hover:opacity-90">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </form>

                <table class="w-full text-sm">
                    <thead>
                        <tr class="font-semibold text-gray-900 border-b border-gray-200">
                            <th class="py-3 w-12 text-center">No</th>
                            <th class="py-3 text-center">Return ID</th>
                            <th class="py-3 text-center">Borrower</th>
                            <th class="py-3 text-center">Tool Name</th>
                            <th class="py-3 text-center">Return Date</th>
                            <th class="py-3 text-center">Condition</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 w-20 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($returns as $r)
                            @php $br = $r->borrowing; @endphp
                            <tr class="border-b border-gray-200 last:border-0">
                                <td class="py-3 text-center">{{ $returns->firstItem() + $loop->index }}</td>
                                <td class="py-3 text-center">{{ $r->return_code }}</td>
                                <td class="py-3 text-center">{{ $br?->borrower?->name ?? '-' }}</td>
                                <td class="py-3 text-center">{{ $br?->tool?->tools_name ?? '-' }}</td>
                                <td class="py-3 text-center">{{ $r->return_date?->format('j/n/y') }}</td>
                                <td class="py-3 text-center font-semibold {{ $r->conditionClass() }}">{{ $r->condition }}</td>
                                <td class="py-3 text-center font-semibold {{ $r->statusClass() }}">{{ $r->display_status }}</td>
                                <td class="py-3 text-center">
                                    <div class="relative inline-block" x-data="{ open: false }" @click.outside="open = false">
                                        <button type="button" @click="open = !open" class="px-2 text-gray-500 hover:text-gray-900">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <div x-show="open" x-cloak x-transition
                                             class="absolute right-0 z-20 mt-1 w-32 bg-white rounded-lg shadow-lg border border-gray-100 text-left text-sm py-1">

                                            <button type="button"
                                                    @click="open = false;
                                                            editReturnAction = '{{ route('transactions.returns.update', $r) }}';
                                                            editReturn = @js([
                                                                'code'       => $r->return_code,
                                                                'borrower'   => $br?->borrower?->name ?? '-',
                                                                'tool'       => $br?->tool?->tools_name ?? '-',
                                                                'borrowDate' => $br?->borrow_date?->format('d/m/Y'),
                                                                'dueDate'    => $br?->due_date?->format('d/m/Y'),
                                                                'dueIso'     => $br?->due_date?->format('Y-m-d'),
                                                                'returnDate' => $r->return_date?->format('Y-m-d'),
                                                                'condition'  => $r->condition,
                                                                'notes'      => $r->notes,
                                                            ]);
                                                            $dispatch('open-modal', 'edit-return')"
                                                    class="block w-full text-left px-4 py-2 hover:bg-gray-50">
                                                <i class="bi bi-pencil mr-2 text-[var(--c-primary)]"></i>Edit
                                            </button>

                                            <button type="button"
                                                    @click="open = false;
                                                            deleteAction = '{{ route('transactions.returns.destroy', $r) }}';
                                                            $dispatch('open-modal', 'confirm-delete')"
                                                    class="block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-50">
                                                <i class="bi bi-trash mr-2"></i>Delete
                                            </button>

                                            <button type="button"
                                                    @click="open = false; $dispatch('show-return-detail', @js($r->detailData()))"
                                                    class="block w-full text-left px-4 py-2 hover:bg-gray-50">
                                                <i class="bi bi-info-circle mr-2 text-[var(--c-primary)]"></i>Detail
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-10 text-center text-gray-500">
                                    No return records found. Try a different keyword or clear the filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="flex items-center justify-between mt-5 text-xs text-gray-600">
                    <label class="flex items-center gap-2">
                        Rows per page
                        <select name="r_per_page" form="return-filter" onchange="this.form.submit()"
                                class="rounded-md border-0 bg-[var(--c-accent)] text-white text-xs py-1 pl-3 pr-7">
                            @foreach ([5, 10, 25] as $n)
                                <option value="{{ $n }}" @selected($rPer === $n)>{{ $n }}</option>
                            @endforeach
                        </select>
                    </label>
                    <div>{{ $returns->links() }}</div>
                </div>
            </div>
        </x-card>

        {{-- form hapus tersembunyi, dipakai Borrowing dan Return --}}
        <form id="delete-form" method="POST" :action="deleteAction" class="hidden">
            @csrf
            @method('DELETE')
        </form>

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

        {{-- modal form: di dalam wrapper supaya membaca state edit di atas --}}
        @include('transactions.add-borrow')
        @include('transactions.edit-borrow')
        @include('transactions.add-return')
        @include('transactions.edit-return')
    </div>

    {{-- modal detail (menyimpan datanya sendiri, menerima event dari tombol Detail) --}}
    @include('transactions.detail-borrow')
    @include('transactions.detail-return')
</x-app-layout>