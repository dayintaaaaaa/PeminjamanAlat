<x-app-layout>
    <x-slot name="header">Return</x-slot>

    <div class="mb-8">
        <h1 class="text-2xl font-bold">Return</h1>
        <p class="text-sm text-gray-500 mt-1">Manage and review equipment return approvals.</p>
    </div>

    <div x-data="{ approveAction: '', rejectAction: '' }">

        <x-card>
            {{-- search --}}
            <form id="filter-form" method="GET" action="{{ route('return.return') }}" class="mb-6">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                       class="h-11 w-full rounded-lg px-6 shadow bg-[var(--c-accent)] text-white text-sm placeholder:text-white/90">
            </form>

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
                    @forelse ($returns as $r)
                        @php $b = $r->borrowing; @endphp
                        <tr class="border-b border-gray-200 last:border-0">
                            <td class="py-3 text-center">{{ $returns->firstItem() + $loop->index }}</td>
                            <td class="py-3 text-center">{{ $r->return_code }}</td>
                            <td class="py-3 text-center">{{ $b?->borrower?->name ?? '-' }}</td>
                            <td class="py-3 text-center">{{ $b?->tool?->tools_name ?? '-' }}</td>
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

                                        @if ($r->status === 'Pending')
                                            <button type="button"
                                                    @click="open = false;
                                                            approveAction = '{{ route('return.approve', $r) }}';
                                                            $dispatch('open-modal', 'confirm-approve')"
                                                    class="block w-full text-left px-4 py-2 hover:bg-gray-50">
                                                <i class="bi bi-check-square mr-2 text-[var(--c-primary)]"></i>Approve
                                            </button>
                                            <button type="button"
                                                    @click="open = false;
                                                            rejectAction = '{{ route('return.reject', $r) }}';
                                                            $dispatch('open-modal', 'confirm-reject')"
                                                    class="block w-full text-left px-4 py-2 hover:bg-gray-50">
                                                <i class="bi bi-x-square mr-2 text-[var(--c-primary)]"></i>Reject
                                            </button>
                                        @endif

                                        {{-- Detail: datanya disiapkan model, modalnya komponen terpisah --}}
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
                                No return records found. Try a different keyword.
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

                <div>{{ $returns->links() }}</div>
            </div>
        </x-card>

        {{-- form tersembunyi; action-nya diisi saat Approve atau Reject diklik --}}
        <form id="approve-form" method="POST" :action="approveAction" class="hidden">
            @csrf
            @method('PATCH')
        </form>
        <form id="reject-form" method="POST" :action="rejectAction" class="hidden">
            @csrf
            @method('PATCH')
        </form>

        <x-alert-modal name="confirm-approve" type="approve" title="Approve Data" message="Are you sure you want to approve this data?">
            <button type="button" x-on:click="$dispatch('close-modal', 'confirm-approve')"
                    class="rounded-md bg-gray-200 px-6 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-300">
                Cancel
            </button>
            <button type="submit" form="approve-form"
                    class="rounded-md bg-red-600 px-6 py-1.5 text-xs font-semibold text-white hover:bg-red-700">
                Yes
            </button>
        </x-alert-modal>

        <x-alert-modal name="confirm-reject" type="reject" title="Reject Data" message="Are you sure you want to reject this data?">
            <button type="button" x-on:click="$dispatch('close-modal', 'confirm-reject')"
                    class="rounded-md bg-gray-200 px-6 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-300">
                Cancel
            </button>
            <button type="submit" form="reject-form"
                    class="rounded-md bg-red-600 px-6 py-1.5 text-xs font-semibold text-white hover:bg-red-700">
                Yes
            </button>
        </x-alert-modal>
    </div>

    {{-- modal detail: dipakai bersama halaman admin --}}
    @include('transactions.detail-return')
</x-app-layout>