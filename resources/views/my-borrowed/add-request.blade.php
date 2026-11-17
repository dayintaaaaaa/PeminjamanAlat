<x-modal name="add-request" :show="$errors->addRequest->isNotEmpty()" maxWidth="2xl">
    <form method="POST" action="{{ route('my-borrowed.store') }}"
          x-data="{ tool: '{{ old('id_tool') }}', stock: @js($tools->pluck('available', 'id')) }">
        @csrf

        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-base font-bold text-gray-900">Add Borrowing</h2>
        </div>

        <div class="space-y-4 px-6 py-5 text-sm">

            {{-- kode + dua tanggal (dibuat otomatis) --}}
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label for="req_code" class="block font-medium text-gray-900 mb-1">Borrowing Code</label>
                    <input type="text" id="req_code" value="{{ $nextCode }}" readonly
                           class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                </div>
                <div>
                    <label for="req_borrow_date" class="mb-1 flex items-center gap-1 font-medium text-gray-900">
                        Borrowing Date <i class="bi bi-calendar3 text-xs"></i>
                    </label>
                    <input type="text" id="req_borrow_date" value="{{ $borrowDate->format('d/m/Y') }}" readonly
                           class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                </div>
                <div>
                    <label for="req_due_date" class="mb-1 flex items-center gap-1 font-medium text-gray-900">
                        Due Date <i class="bi bi-calendar3 text-xs"></i>
                    </label>
                    <input type="text" id="req_due_date" value="{{ $dueDate->format('d/m/Y') }}" readonly
                           class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                </div>
            </div>

            {{-- borrower: selalu user yang login --}}
            <div>
                <label for="req_borrower" class="block font-medium text-gray-900 mb-1">Borrower</label>
                <input type="text" id="req_borrower" value="{{ auth()->user()->name }}" readonly
                       class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
            </div>

            {{-- tool + qty --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-2">
                    <label for="req_tool" class="block font-medium text-gray-900 mb-1">Tool Name</label>
                    <select id="req_tool" name="id_tool" x-model="tool" required
                            class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm text-gray-900 focus:border-[var(--c-primary)] focus:ring-0">
                        <option value="" disabled>Select tool name</option>
                        @foreach ($tools as $t)
                            <option value="{{ $t->id }}">{{ $t->tools_name }} ({{ $t->available }} available)</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->addRequest->get('id_tool')" class="mt-1" />
                </div>
                <div>
                    <label for="req_qty" class="block font-medium text-gray-900 mb-1">Qty</label>
                    <input type="number" id="req_qty" name="qty" value="{{ old('qty') }}" min="1" :max="stock[tool] ?? null" required
                           placeholder="Enter qty.."
                           class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-[var(--c-primary)] focus:ring-0">
                    <x-input-error :messages="$errors->addRequest->get('qty')" class="mt-1" />
                </div>
            </div>

            {{-- notes --}}
            <div>
                <label for="req_notes" class="block font-medium text-gray-900 mb-1">Notes</label>
                <textarea id="req_notes" name="notes" rows="3" placeholder="Enter notes (optional).."
                          class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-[var(--c-primary)] focus:ring-0">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-between px-6 pb-5">
            <button type="button" x-on:click="$dispatch('close-modal', 'add-request')"
                    class="rounded-md bg-gray-300 px-5 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-400">
                Cancel
            </button>
            <button type="submit"
                    class="rounded-md bg-[var(--c-primary)] px-6 py-2 text-xs font-semibold text-white hover:opacity-90">
                Save
            </button>
        </div>
    </form>
</x-modal>