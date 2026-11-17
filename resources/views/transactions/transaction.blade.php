<x-app-layout>
    <x-slot name="header">Transaction</x-slot>

    @php
    // data contoh, nanti diganti data dari database
    $borrowings = [
    ['id' => 'TRX-02', 'borrower' => 'David Adrison S', 'tool' => 'Digital Multimeter', 'borrow' => '27/9/26', 'due' => '02/10/26', 'status' => 'Active'],
    ['id' => 'TRX-01', 'borrower' => 'Lee Jeno', 'tool' => 'Soldering Station', 'borrow' => '25/9/26', 'due' => '30/9/26', 'status' => 'Overdue'],
    ];

    // class ditulis utuh supaya dikenali Tailwind
    $statusColor = [
    'Active' => 'text-green-600',
    'Overdue' => 'text-red-600',
    ];

    $returns = [
    ['id' => 'RET-02', 'borrower' => 'David Adrison S', 'tool' => 'Digital Multimeter', 'date' => '25/5/26', 'condition' => 'Good', 'status' => 'On Time', 'late' => false],
    ['id' => 'RET-01', 'borrower' => 'Lee Jeno', 'tool' => 'Soldering Station', 'date' => '25/5/26', 'condition' => 'Lost', 'status' => '2 Days Late', 'late' => true],
    ];

    // class ditulis utuh supaya dikenali Tailwind
    $conditionColor = [
    'Good' => 'text-green-600',
    'Damaged' => 'text-orange-500',
    'Lost' => 'text-red-600',
    ];
    @endphp

    {{-- buat judul sama sub judul --}}
    <div class="flex items-start justify-between mb-8">
        <div>
            <h1 class="font-bold text-2xl">Transaction</h1>
            <p class="text-sm text-gray-500 mt-1">Manage and monitor equipment borrowing and return records.</p>
        </div>
    </div>

    <x-card>
        <div x-data="{ tab: 'borrowing' }">

            {{-- tab --}}
            <div class="flex border-b border-gray-200 mb-5 text-sm">
                <button type="button" @click="tab = 'borrowing'"
                    :class="tab === 'borrowing' ? 'border-[var(--c-primary)] font-semibold text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-900'"
                    class="px-4 py-2 border-b-2 -mb-px">
                    Borrowing
                </button>
                <button type="button" @click="tab = 'return'"
                    :class="tab === 'return' ? 'border-[var(--c-primary)] font-semibold text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-900'"
                    class="px-4 py-2 border-b-2 -mb-px">
                    Return
                </button>
            </div>

            {{-- ===== TAB BORROWING ===== --}}
            <div x-show="tab === 'borrowing'">

                {{-- search + 3 tombol ikon --}}
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
                    <button type="button" title="Add borrowing"
                        @click="$dispatch('open-modal', 'add-borrowing')"
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-[var(--c-accent)] text-white hover:opacity-90">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>

                {{-- tabel --}}
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
                        @foreach ($borrowings as $i => $b)
                        <tr class="border-b border-gray-200 last:border-0">
                            <td class="py-3 text-center">{{ $i + 1 }}</td>
                            <td class="py-3 text-center">{{ $b['id'] }}</td>
                            <td class="py-3 text-center">{{ $b['borrower'] }}</td>
                            <td class="py-3 text-center">{{ $b['tool'] }}</td>
                            <td class="py-3 text-center">{{ $b['borrow'] }}</td>
                            <td class="py-3 text-center">{{ $b['due'] }}</td>
                            <td class="py-3 text-center font-semibold {{ $statusColor[$b['status']] }}">{{ $b['status'] }}</td>
                            <td class="py-3 text-center">
                                <div class="relative inline-block" x-data="{ open: false }" @click.outside="open = false">
                                    <button type="button" @click="open = !open" class="px-2 text-gray-500 hover:text-gray-900">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <div x-show="open" x-cloak x-transition class="absolute right-0 z-20 mt-1 w-32 bg-white rounded-lg shadow-lg border border-gray-100 text-left text-sm py-1">
                                        <button type="button"
                                            @click="open = false; $dispatch('open-modal', 'edit-borrowing')"
                                            class="block w-full text-left px-4 py-2 hover:bg-gray-50">
                                            <i class="bi bi-pencil mr-2"></i>Edit
                                        </button>
                                        <button type="button"
                                            @click="open = false; $dispatch('open-modal', 'confirm-delete')"
                                            class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-50">
                                            <i class="bi bi-trash mr-2"></i>Delete
                                        </button>
                                        <button type="button"
                                            @click="open = false; $dispatch('open-modal', 'detail-borrowing')"
                                            class="block w-full text-left px-4 py-2 hover:bg-gray-50">
                                            <i class="bi bi-info-circle mr-2"></i>Detail
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
            </div>

            {{-- ===== TAB RETURN ===== --}}
            <div x-show="tab === 'return'" x-cloak>

                {{-- search + 3 tombol ikon --}}
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
                    <button type="button" title="Add return"
                        @click="$dispatch('open-modal', 'add-return')"
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-[var(--c-accent)] text-white hover:opacity-90">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>

                {{-- tabel --}}
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
                        @foreach ($returns as $i => $r)
                        <tr class="border-b border-gray-200 last:border-0">
                            <td class="py-3 text-center">{{ $i + 1 }}</td>
                            <td class="py-3 text-center">{{ $r['id'] }}</td>
                            <td class="py-3 text-center">{{ $r['borrower'] }}</td>
                            <td class="py-3 text-center">{{ $r['tool'] }}</td>
                            <td class="py-3 text-center">{{ $r['date'] }}</td>
                            <td class="py-3 text-center font-semibold {{ $conditionColor[$r['condition']] }}">{{ $r['condition'] }}</td>
                            <td class="py-3 text-center font-semibold {{ $r['late'] ? 'text-red-600' : 'text-green-600' }}">{{ $r['status'] }}</td>
                            <td class="py-3 text-center">
                                <div class="relative inline-block" x-data="{ open: false }" @click.outside="open = false">
                                    <button type="button" @click="open = !open" class="px-2 text-gray-500 hover:text-gray-900">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <div x-show="open" x-cloak x-transition
                                        class="absolute right-0 z-20 mt-1 w-32 bg-white rounded-lg shadow-lg border border-gray-100 text-left text-sm py-1">
                                        <button type="button"
                                            @click="open = false; $dispatch('open-modal', 'edit-return')"
                                            class="block w-full text-left px-4 py-2 hover:bg-gray-50">
                                            <i class="bi bi-pencil mr-2"></i>Edit
                                        </button>
                                        <button type="button"
                                            @click="open = false; $dispatch('open-modal', 'confirm-delete')"
                                            class="block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-50">
                                            <i class="bi bi-trash mr-2"></i>Delete
                                        </button>
                                        <button type="button"
                                            @click="open = false; $dispatch('open-modal', 'detail-return')"
                                            class="block w-full text-left px-4 py-2 hover:bg-gray-50">
                                            <i class="bi bi-info-circle mr-2"></i>Detail
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
            </div>
        </div>
    </x-card>

    @include('transactions.add-borrow')
    @include('transactions.edit-borrow')
    @include('transactions.detail-borrow')
    @include('transactions.add-return')
    @include('transactions.edit-return')
    @include('transactions.detail-return')

    <x-alert-modal name="confirm-delete" type="confirm" title="Delete data" message="Are you sure delete this data?">
        <button type="button" x-on:click="$dispatch('close-modal', 'confirm-delete')"
            class="rounded-md bg-gray-200 px-6 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-300">
            Cancel
        </button>
        <button type="button"
            x-on:click="
                $dispatch('close-modal', 'confirm-delete');
                $dispatch('open-modal', 'success-delete');
                setTimeout(() => $dispatch('close-modal', 'success-delete'), 2000)
            "
            class="rounded-md bg-red-600 px-6 py-1.5 text-xs font-semibold text-white hover:bg-red-700">
            Delete
        </button>
    </x-alert-modal>

    <x-alert-modal name="success-delete" type="success" title="Success" message="Successfully deleted this data" />

    <x-alert-modal name="error-delete" type="error" title="Failed" message="Failed to delete this data. Please try again.">
        <button type="button" x-on:click="$dispatch('close-modal', 'error-delete')"
            class="rounded-md bg-gray-200 px-8 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-300">
            Close
        </button>
    </x-alert-modal>
</x-app-layout>