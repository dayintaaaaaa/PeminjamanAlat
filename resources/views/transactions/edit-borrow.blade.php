<x-modal name="edit-borrowing" maxWidth="2xl">
    <form method="POST" :action="editBorrowAction">
        @csrf
        @method('PUT')

        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-base font-bold text-gray-900">Edit Borrowing</h2>
        </div>

        <div class="space-y-4 px-6 py-5 text-sm">

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block font-medium text-gray-900 mb-1">Borrowing Code</label>
                    <input type="text" x-model="editBorrow.code" readonly
                           class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                </div>
                <div>
                    <label class="mb-1 flex items-center gap-1 font-medium text-gray-900">Borrowing Date <i class="bi bi-calendar3 text-xs"></i></label>
                    <input type="text" x-model="editBorrow.borrowDate" readonly
                           class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                </div>
                <div>
                    <label class="mb-1 flex items-center gap-1 font-medium text-gray-900">Due Date <i class="bi bi-calendar3 text-xs"></i></label>
                    <input type="text" x-model="editBorrow.dueDate" readonly
                           class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                </div>
            </div>

            <div>
                <label for="edit_b_user" class="block font-medium text-gray-900 mb-1">Borrower</label>
                <select id="edit_b_user" name="id_user" x-model="editBorrow.id_user" required
                        class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm text-gray-900 focus:border-[var(--c-primary)] focus:ring-0">
                    @foreach ($borrowers as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-2">
                    <label for="edit_b_tool" class="block font-medium text-gray-900 mb-1">Tool Name</label>
                    <select id="edit_b_tool" name="id_tools" x-model="editBorrow.id_tools" required
                            class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm text-gray-900 focus:border-[var(--c-primary)] focus:ring-0">
                        @foreach ($tools as $t)
                            <option value="{{ $t->id }}">{{ $t->tools_name }} ({{ $t->available }} available)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="edit_b_qty" class="block font-medium text-gray-900 mb-1">Qty</label>
                    <input type="number" id="edit_b_qty" name="borrow_qty" x-model="editBorrow.qty" min="1" required placeholder="Enter qty.."
                           class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-[var(--c-primary)] focus:ring-0">
                </div>
            </div>

            <div>
                <label for="edit_b_notes" class="block font-medium text-gray-900 mb-1">Notes</label>
                <textarea id="edit_b_notes" name="notes" x-model="editBorrow.notes" rows="3" placeholder="Enter notes (optional).."
                          class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-[var(--c-primary)] focus:ring-0"></textarea>
            </div>
        </div>

        <div class="flex items-center justify-between px-6 pb-5">
            <button type="button" x-on:click="$dispatch('close-modal', 'edit-borrowing')"
                    class="rounded-md bg-gray-300 px-5 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-400">Cancel</button>
            <button type="submit"
                    class="rounded-md bg-[var(--c-primary)] px-6 py-2 text-xs font-semibold text-white hover:opacity-90">Save</button>
        </div>
    </form>
</x-modal>