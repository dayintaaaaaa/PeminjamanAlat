<x-modal name="add-return" maxWidth="2xl">
    <form method="POST" action="#">
        @csrf

        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-base font-bold text-gray-900">Add Return</h2>
        </div>

        <div class="grid grid-cols-2 gap-x-6 px-6 py-5 text-sm">

            {{-- ===== KOLOM KIRI ===== --}}
            <div class="space-y-3">
                <div>
                    <label for="add_ret_borrow" class="block font-medium text-gray-900 mb-1">Active Borrow</label>
                    <select id="add_ret_borrow" name="borrow_id" required
                            class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm text-gray-900 focus:border-[var(--c-primary)] focus:ring-0">
                        <option>TRX-02 | David Adrison S</option>
                        <option>TRX-01 | Lee Jeno</option>
                    </select>
                </div>

                <div>
                    <label for="add_ret_borrower" class="block font-medium text-gray-900 mb-1">Borrower</label>
                    <input type="text" id="add_ret_borrower" value="David Adrison S" readonly
                           class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                </div>

                <div>
                    <label for="add_ret_tool" class="block font-medium text-gray-900 mb-1">Tool Name</label>
                    <input type="text" id="add_ret_tool" value="Digital Multimeter" readonly
                           class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="add_ret_borrow_date" class="mb-1 flex items-center gap-1 font-medium text-gray-900">
                            Borrowing Date <i class="bi bi-calendar3 text-xs"></i>
                        </label>
                        <input type="text" id="add_ret_borrow_date" value="28/09/2026" readonly
                               class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                    </div>
                    <div>
                        <label for="add_ret_due" class="mb-1 flex items-center gap-1 font-medium text-gray-900">
                            Due Date <i class="bi bi-calendar3 text-xs"></i>
                        </label>
                        <input type="text" id="add_ret_due" value="03/10/2026" readonly
                               class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                    </div>
                </div>
            </div>

            {{-- ===== KOLOM KANAN ===== --}}
            <div class="space-y-3">
                <div class="grid grid-cols-5 gap-3">
                    <div class="col-span-3">
                        <label for="add_ret_date" class="block font-medium text-gray-900 mb-1">Return Date</label>
                        <input type="date" id="add_ret_date" name="return_date" value="2026-10-03" required
                               class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm text-gray-900 focus:border-[var(--c-primary)] focus:ring-0">
                    </div>
                    <div class="col-span-2">
                        <label for="add_ret_code" class="block font-medium text-gray-900 mb-1">Return Code</label>
                        <input type="text" id="add_ret_code" name="code" value="RET-03" readonly
                               class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                    </div>
                </div>

                <div>
                    <label for="add_ret_condition" class="block font-medium text-gray-900 mb-1">Condition</label>
                    <select id="add_ret_condition" name="condition" required
                            class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm text-gray-900 focus:border-[var(--c-primary)] focus:ring-0">
                        <option>Good</option>
                        <option>Damaged</option>
                        <option>Lost</option>
                    </select>
                </div>

                <div>
                    <label for="add_ret_status" class="block font-medium text-gray-900 mb-1">Status</label>
                    <input type="text" id="add_ret_status" value="On Time (0 Days)" readonly
                           class="w-full cursor-default rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm text-gray-900 focus:border-[var(--c-accent)] focus:ring-0">
                </div>

                <div>
                    <label for="add_ret_notes" class="block font-medium text-gray-900 mb-1">Notes</label>
                    <textarea id="add_ret_notes" name="notes" rows="3" placeholder="Enter notes (optional).."
                              class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-[var(--c-primary)] focus:ring-0"></textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between px-6 pb-5">
            <button type="button" x-on:click="$dispatch('close-modal', 'add-return')"
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