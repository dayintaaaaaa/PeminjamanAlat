<x-modal name="add-return" :show="$errors->addReturn->isNotEmpty()" maxWidth="2xl">
    <form method="POST" action="{{ route('transactions.returns.store') }}"
          x-data="{
              id: '{{ old('id_borrowing') }}',
              borrows: @js($borrowMap),
              returnDate: '{{ old('return_date', today()->format('Y-m-d')) }}',
              get sel() { return this.borrows[this.id] ?? null; },
              get statusText() {
                  if (!this.sel || !this.returnDate) return '';
                  const late = Math.round((new Date(this.returnDate) - new Date(this.sel.dueIso)) / 86400000);
                  return late > 0 ? 'Late (' + late + ' Days)' : 'On Time (0 Days)';
              }
          }">
        @csrf

        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-base font-bold text-gray-900">Add Return</h2>
        </div>

        <div class="grid grid-cols-2 gap-x-6 px-6 py-5 text-sm">

            {{-- kiri --}}
            <div class="space-y-3">
                <div>
                    <label for="add_r_borrow" class="block font-medium text-gray-900 mb-1">Active Borrow</label>
                    <select id="add_r_borrow" name="id_borrowing" x-model="id" required
                            class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm text-gray-900 focus:border-[var(--c-primary)] focus:ring-0">
                        <option value="" disabled>{{ count($borrowMap) ? 'Select active borrow' : 'No active borrowing available' }}</option>
                        @foreach ($borrowMap as $key => $item)
                            <option value="{{ $key }}">{{ $item['label'] }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->addReturn->get('id_borrowing')" class="mt-1" />
                </div>

                <div>
                    <label class="block font-medium text-gray-900 mb-1">Borrower</label>
                    <input type="text" :value="sel ? sel.borrower : ''" readonly
                           class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                </div>

                <div>
                    <label class="block font-medium text-gray-900 mb-1">Tool Name</label>
                    <input type="text" :value="sel ? sel.tool : ''" readonly
                           class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1 flex items-center gap-1 font-medium text-gray-900">Borrowing Date <i class="bi bi-calendar3 text-xs"></i></label>
                        <input type="text" :value="sel ? sel.borrowDate : ''" readonly
                               class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                    </div>
                    <div>
                        <label class="mb-1 flex items-center gap-1 font-medium text-gray-900">Due Date <i class="bi bi-calendar3 text-xs"></i></label>
                        <input type="text" :value="sel ? sel.dueDate : ''" readonly
                               class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                    </div>
                </div>
            </div>

            {{-- kanan --}}
            <div class="space-y-3">
                <div class="grid grid-cols-5 gap-3">
                    <div class="col-span-3">
                        <label for="add_r_date" class="block font-medium text-gray-900 mb-1">Return Date</label>
                        <input type="date" id="add_r_date" name="return_date" x-model="returnDate" required
                               class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm text-gray-900 focus:border-[var(--c-primary)] focus:ring-0">
                        <x-input-error :messages="$errors->addReturn->get('return_date')" class="mt-1" />
                    </div>
                    <div class="col-span-2">
                        <label class="block font-medium text-gray-900 mb-1">Return Code</label>
                        <input type="text" value="{{ $nextReturnCode }}" readonly
                               class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                    </div>
                </div>

                <div>
                    <label for="add_r_condition" class="block font-medium text-gray-900 mb-1">Condition</label>
                    <select id="add_r_condition" name="condition" required
                            class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm text-gray-900 focus:border-[var(--c-primary)] focus:ring-0">
                        @foreach (['Good', 'Damaged', 'Lost'] as $c)
                            <option value="{{ $c }}" @selected(old('condition', 'Good') === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-medium text-gray-900 mb-1">Status</label>
                    <input type="text" :value="statusText" readonly placeholder="Calculated automatically"
                           class="w-full cursor-default rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:border-[var(--c-accent)] focus:ring-0">
                </div>

                <div>
                    <label for="add_r_notes" class="block font-medium text-gray-900 mb-1">Notes</label>
                    <textarea id="add_r_notes" name="notes" rows="3" placeholder="Enter notes (optional).."
                              class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-[var(--c-primary)] focus:ring-0">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between px-6 pb-5">
            <button type="button" x-on:click="$dispatch('close-modal', 'add-return')"
                    class="rounded-md bg-gray-300 px-5 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-400">Cancel</button>
            <button type="submit"
                    class="rounded-md bg-[var(--c-primary)] px-6 py-2 text-xs font-semibold text-white hover:opacity-90">Save</button>
        </div>
    </form>
</x-modal>