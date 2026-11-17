<x-modal name="add-borrowing" maxWidth="2xl">
    <form method="POST" action="#">
        @csrf

        {{-- judul --}}
        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-base font-bold text-gray-900">Add Borrowing</h2>
        </div>

        <div class="space-y-4 px-6 py-5 text-sm">

            {{-- baris 1: kode + 2 tanggal --}}
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label for="borrow_code" class="block font-medium text-gray-900 mb-1">Borrowing Code</label>
                    <input type="text" id="borrow_code" name="code" value="TRX-04" readonly
                           class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                </div>
                <div>
                    <label for="borrow_date" class="mb-1 flex items-center gap-1 font-medium text-gray-900">
                        Borrowing Date <i class="bi bi-calendar3 text-xs"></i>
                    </label>
                    <input type="text" id="borrow_date" name="borrow_date" value="01/10/2026" readonly
                           class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                </div>
                <div>
                    <label for="due_date" class="mb-1 flex items-center gap-1 font-medium text-gray-900">
                        Due Date <i class="bi bi-calendar3 text-xs"></i>
                    </label>
                    <input type="text" id="due_date" name="due_date" value="06/10/2026" readonly
                           class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                </div>
            </div>

            {{-- baris 2: borrower --}}
            <div>
                <label for="borrower" class="block font-medium text-gray-900 mb-1">Borrower</label>
                <select id="borrower" name="borrower" required
                        class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm text-gray-900 focus:border-[var(--c-primary)] focus:ring-0">
                    <option value="" disabled selected>Select borrower</option>
                    <option>David Adrison S</option>
                    <option>Lee Jeno</option>
                </select>
            </div>

            {{-- baris 3: tool + qty --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-2">
                    <label for="tool" class="block font-medium text-gray-900 mb-1">Tool Name</label>
                    <select id="tool" name="tool" required
                            class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm text-gray-900 focus:border-[var(--c-primary)] focus:ring-0">
                        <option value="" disabled selected>Select tool name</option>
                        <option>Digital Multimeter</option>
                        <option>Soldering Station</option>
                        <option>Wire Stripper</option>
                        <option>Safety Goggles</option>
                    </select>
                </div>
                <div>
                    <label for="qty" class="block font-medium text-gray-900 mb-1">Qty</label>
                    <input type="number" id="qty" name="qty" min="1" required placeholder="Enter qty.."
                           class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-[var(--c-primary)] focus:ring-0">
                </div>
            </div>

            {{-- baris 4: notes --}}
            <div>
                <label for="notes" class="block font-medium text-gray-900 mb-1">Notes</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Enter notes (opsional).."
                          class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-[var(--c-primary)] focus:ring-0"></textarea>
            </div>
        </div>

        {{-- tombol --}}
        <div class="flex items-center justify-between px-6 pb-5">
            <button type="button" x-on:click="$dispatch('close-modal', 'add-borrowing')"
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