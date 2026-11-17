<x-modal name="detail-return" maxWidth="2xl">
    <div class="border-b border-gray-300 px-6 py-4">
        <h2 class="text-base font-bold text-gray-900">Return Transaction Detail</h2>
    </div>

    <div class="px-6 text-sm text-gray-800">

        {{-- bagian 1: peminjam + status + kondisi --}}
        <div class="border-b border-gray-300 py-4">
            <div class="divide-y-2 divide-white bg-gray-100">
                <div class="flex items-center justify-between px-3 py-1.5">
                    <div class="flex gap-3">
                        <span class="w-24">Borrower</span><span>:</span><span>David Adrison S</span>
                    </div>
                    <div class="flex w-44 gap-3">
                        <span class="w-16">Status</span><span>:</span>
                        <span class="font-semibold text-green-600">On Time</span>
                    </div>
                </div>
                <div class="flex items-center justify-between px-3 py-1.5">
                    <div class="flex gap-3">
                        <span class="w-24">Return ID</span><span>:</span><span>RET-02</span>
                    </div>
                    <div class="flex w-44 gap-3">
                        <span class="w-16">Condition</span><span>:</span>
                        <span class="font-semibold text-green-600">Good</span>
                    </div>
                </div>
                <div class="flex gap-3 px-3 py-1.5">
                    <span class="w-24">Return At</span><span>:</span><span>02-10-2026</span>
                </div>
            </div>
        </div>

        {{-- bagian 2: foto + info alat --}}
        <div class="flex gap-4 border-b border-gray-300 py-4">
            <div class="flex h-24 w-32 shrink-0 items-center justify-center rounded bg-gray-300">
                <i class="bi bi-camera text-4xl text-gray-500"></i>
            </div>

            <div class="flex-1 divide-y-2 divide-white bg-gray-100">
                <div class="flex gap-3 px-3 py-1.5">
                    <span class="w-24">Tool</span><span>:</span><span>MT-01 | Digital Multimeter</span>
                </div>
                <div class="flex gap-3 px-3 py-1.5">
                    <span class="w-24">Category</span><span>:</span><span>MT-27926 | Measurement Tools</span>
                </div>
                <div class="flex gap-3 px-3 py-1.5">
                    <span class="w-24">Qty Return</span><span>:</span><span>1</span>
                </div>
                <div class="flex gap-3 px-3 py-1.5">
                    <span class="w-24">Location</span><span>:</span><span>Rack A - Shelf 2</span>
                </div>
            </div>
        </div>

        {{-- bagian 3: tanggal + catatan --}}
        <div class="py-4">
            <div class="divide-y-2 divide-white bg-gray-100">
                <div class="flex items-center justify-between px-3 py-1.5">
                    <div class="flex gap-3">
                        <span class="w-24">Borrow ID</span><span>:</span><span>TRX-02</span>
                    </div>
                    <div class="flex w-52 gap-3">
                        <span class="w-24">Borrow Date</span><span>:</span><span>28-09-2026</span>
                    </div>
                </div>
                <div class="flex items-center justify-between px-3 py-1.5">
                    <div class="flex gap-3">
                        <span class="w-24">Return Date</span><span>:</span><span>03-10-2026</span>
                    </div>
                    <div class="flex w-52 gap-3">
                        <span class="w-24">Due Date</span><span>:</span><span>03-10-2026</span>
                    </div>
                </div>
                <div class="flex gap-3 px-3 py-1.5">
                    <span class="w-24">Processed By</span><span>:</span><span>James Januar</span>
                </div>
                <div class="flex gap-3 px-3 py-1.5">
                    <span class="w-24">Note</span><span>:</span><span>-</span>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end px-6 pb-5">
        <button type="button" x-on:click="$dispatch('close-modal', 'detail-return')"
                class="rounded-md bg-gray-300 px-6 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-400">
            Cancel
        </button>
    </div>
</x-modal>