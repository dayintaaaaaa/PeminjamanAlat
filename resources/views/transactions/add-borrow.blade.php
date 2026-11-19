<x-modal name="add-borrowing" :show="$errors->addBorrowing->isNotEmpty()" maxWidth="2xl">
    <form method="POST" action="{{ route('transactions.borrowings.store') }}"
          x-data="{ tool: '{{ old('id_tools') }}', stock: @js($tools->pluck('available', 'id')->all()) }">
        @csrf

        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-base font-bold text-gray-900">Add Borrowing</h2>
        </div>

        <div class="space-y-4 px-6 py-5 text-sm">

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block font-medium text-gray-900 mb-1">Borrowing Code</label>
                    <input type="text" value="{{ $nextBorrowCode }}" readonly
                           class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                </div>
                <div>
                    <label class="mb-1 flex items-center gap-1 font-medium text-gray-900">Borrowing Date <i class="bi bi-calendar3 text-xs"></i></label>
                    <input type="text" value="{{ $borrowDate->format('d/m/Y') }}" readonly
                           class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                </div>
                <div>
                    <label class="mb-1 flex items-center gap-1 font-medium text-gray-900">Due Date <i class="bi bi-calendar3 text-xs"></i></label>
                    <input type="text" value="{{ $dueDate->format('d/m/Y') }}" readonly
                           class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                </div>
            </div>

            <div>
                <label for="add_b_user" class="block font-medium text-gray-900 mb-1">Borrower</label>
                <select id="add_b_user" name="id_user" required
                        class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm text-gray-900 focus:border-[var(--c-primary)] focus:ring-0">
                    <option value="" disabled @selected(! old('id_user'))>Select borrower</option>
                    @foreach ($borrowers as $u)
                        <option value="{{ $u->id }}" @selected((string) old('id_user') === (string) $u->id)>{{ $u->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->addBorrowing->get('id_user')" class="mt-1" />
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-2">
                    <label for="add_b_tool" class="block font-medium text-gray-900 mb-1">Tool Name</label>
                    <select id="add_b_tool" name="id_tools" x-model="tool" required
                            class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm text-gray-900 focus:border-[var(--c-primary)] focus:ring-0">
                        <option value="" disabled>Select tool name</option>
                        @foreach ($tools->where('available', '>', 0)->where('status', '!=', 'Maintenance') as $t)
                            <option value="{{ $t->id }}">{{ $t->tools_name }} ({{ $t->available }} available)</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->addBorrowing->get('id_tools')" class="mt-1" />
                </div>
                <div>
                    <label for="add_b_qty" class="block font-medium text-gray-900 mb-1">Qty</label>
                    <input type="number" id="add_b_qty" name="borrow_qty" value="{{ old('borrow_qty') }}" min="1"
                           :max="stock[tool] ?? null" required placeholder="Enter qty.."
                           class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-[var(--c-primary)] focus:ring-0">
                    <x-input-error :messages="$errors->addBorrowing->get('borrow_qty')" class="mt-1" />
                </div>
            </div>

            <div>
                <label for="add_b_notes" class="block font-medium text-gray-900 mb-1">Notes</label>
                <textarea id="add_b_notes" name="notes" rows="3" placeholder="Enter notes (optional).."
                          class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-[var(--c-primary)] focus:ring-0">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-between px-6 pb-5">
            <button type="button" x-on:click="$dispatch('close-modal', 'add-borrowing')"
                    class="rounded-md bg-gray-300 px-5 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-400">Cancel</button>
            <button type="submit"
                    class="rounded-md bg-[var(--c-primary)] px-6 py-2 text-xs font-semibold text-white hover:opacity-90">Save</button>
        </div>
    </form>
</x-modal>