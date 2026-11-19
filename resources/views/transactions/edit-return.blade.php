<x-modal name="edit-return" maxWidth="2xl">
    <form method="POST" :action="editReturnAction">
        @csrf
        @method('PUT')

        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-base font-bold text-gray-900">Edit Return</h2>
        </div>

        <div class="grid grid-cols-2 gap-x-6 px-6 py-5 text-sm">

            {{-- kiri --}}
            <div class="space-y-3">
                <div>
                    <label class="block font-medium text-gray-900 mb-1">Borrower</label>
                    <input type="text" x-model="editReturn.borrower" readonly
                           class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                </div>
                <div>
                    <label class="block font-medium text-gray-900 mb-1">Tool Name</label>
                    <input type="text" x-model="editReturn.tool" readonly
                           class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1 flex items-center gap-1 font-medium text-gray-900">Borrowing Date <i class="bi bi-calendar3 text-xs"></i></label>
                        <input type="text" x-model="editReturn.borrowDate" readonly
                               class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                    </div>
                    <div>
                        <label class="mb-1 flex items-center gap-1 font-medium text-gray-900">Due Date <i class="bi bi-calendar3 text-xs"></i></label>
                        <input type="text" x-model="editReturn.dueDate" readonly
                               class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                    </div>
                </div>
            </div>

            {{-- kanan --}}
            <div class="space-y-3">
                <div class="grid grid-cols-5 gap-3">
                    <div class="col-span-3">
                        <label for="edit_r_date" class="block font-medium text-gray-900 mb-1">Return Date</label>
                        <input type="date" id="edit_r_date" name="return_date" x-model="editReturn.returnDate" required
                               class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm text-gray-900 focus:border-[var(--c-primary)] focus:ring-0">
                    </div>
                    <div class="col-span-2">
                        <label class="block font-medium text-gray-900 mb-1">Return Code</label>
                        <input type="text" x-model="editReturn.code" readonly
                               class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                    </div>
                </div>

                <div>
                    <label for="edit_r_condition" class="block font-medium text-gray-900 mb-1">Condition</label>
                    <select id="edit_r_condition" name="condition" x-model="editReturn.condition" required
                            class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm text-gray-900 focus:border-[var(--c-primary)] focus:ring-0">
                        @foreach (['Good', 'Damaged', 'Lost'] as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-medium text-gray-900 mb-1">Status</label>
                    <input type="text" :value="editReturnStatus" readonly
                           class="w-full cursor-default rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm text-gray-900 focus:border-[var(--c-accent)] focus:ring-0">
                </div>

                <div>
                    <label for="edit_r_notes" class="block font-medium text-gray-900 mb-1">Notes</label>
                    <textarea id="edit_r_notes" name="notes" x-model="editReturn.notes" rows="3" placeholder="Enter notes (optional).."
                              class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-[var(--c-primary)] focus:ring-0"></textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between px-6 pb-5">
            <button type="button" x-on:click="$dispatch('close-modal', 'edit-return')"
                    class="rounded-md bg-gray-300 px-5 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-400">Cancel</button>
            <button type="submit"
                    class="rounded-md bg-[var(--c-primary)] px-6 py-2 text-xs font-semibold text-white hover:opacity-90">Save</button>
        </div>
    </form>
</x-modal>