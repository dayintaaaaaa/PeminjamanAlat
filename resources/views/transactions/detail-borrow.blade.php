<div x-data="{
        detail: { borrower: '', status: '', statusClass: '', code: '', created: '', tool: '', category: '', qty: 0, location: '', photo: null, borrowDate: '', dueDate: '', processedBy: '', note: '' }
     }"
     x-on:show-borrow-detail.window="detail = $event.detail; $dispatch('open-modal', 'detail-borrow')">

    <x-modal name="detail-borrow" maxWidth="2xl">
        <div class="border-b border-gray-300 px-6 py-4">
            <h2 class="text-base font-bold text-gray-900">Borrowing Transaction Detail</h2>
        </div>

        <div class="px-6 text-sm text-gray-800">

            {{-- bagian 1: peminjam + status --}}
            <div class="border-b border-gray-300 py-4">
                <div class="divide-y-2 divide-white bg-gray-100">
                    <div class="flex items-center justify-between px-3 py-1.5">
                        <div class="flex gap-3">
                            <span class="w-28">Borrower</span><span>:</span><span x-text="detail.borrower"></span>
                        </div>
                        <div class="flex gap-3">
                            <span>Status</span><span>:</span>
                            <span class="font-semibold" :class="detail.statusClass" x-text="detail.status"></span>
                        </div>
                    </div>
                    <div class="flex gap-3 px-3 py-1.5">
                        <span class="w-28">Transaction ID</span><span>:</span><span x-text="detail.code"></span>
                    </div>
                    <div class="flex gap-3 px-3 py-1.5">
                        <span class="w-28">Created At</span><span>:</span><span x-text="detail.created"></span>
                    </div>
                </div>
            </div>

            {{-- bagian 2: foto + info alat --}}
            <div class="flex gap-4 border-b border-gray-300 py-4">
                <div class="flex h-24 w-32 shrink-0 items-center justify-center overflow-hidden rounded bg-gray-300">
                    <template x-if="detail.photo">
                        <img :src="detail.photo" alt="" class="h-full w-full object-cover">
                    </template>
                    <template x-if="!detail.photo">
                        <i class="bi bi-camera text-4xl text-gray-500"></i>
                    </template>
                </div>

                <div class="flex-1 divide-y-2 divide-white bg-gray-100">
                    <div class="flex gap-3 px-3 py-1.5">
                        <span class="w-24">Tool</span><span>:</span><span x-text="detail.tool"></span>
                    </div>
                    <div class="flex gap-3 px-3 py-1.5">
                        <span class="w-24">Category</span><span>:</span><span x-text="detail.category"></span>
                    </div>
                    <div class="flex gap-3 px-3 py-1.5">
                        <span class="w-24">Qty Borrow</span><span>:</span><span x-text="detail.qty"></span>
                    </div>
                    <div class="flex gap-3 px-3 py-1.5">
                        <span class="w-24">Location</span><span>:</span><span x-text="detail.location"></span>
                    </div>
                </div>
            </div>

            {{-- bagian 3: tanggal + catatan --}}
            <div class="py-4">
                <div class="divide-y-2 divide-white bg-gray-100">
                    <div class="flex gap-3 px-3 py-1.5">
                        <span class="w-28">Borrow Date</span><span>:</span><span x-text="detail.borrowDate"></span>
                    </div>
                    <div class="flex gap-3 px-3 py-1.5">
                        <span class="w-28">Due Date</span><span>:</span><span x-text="detail.dueDate"></span>
                    </div>
                    <div class="flex gap-3 px-3 py-1.5">
                        <span class="w-28">Processed By</span><span>:</span><span x-text="detail.processedBy"></span>
                    </div>
                    <div class="flex gap-3 px-3 py-1.5">
                        <span class="w-28">Note</span><span>:</span><span x-text="detail.note"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end px-6 pb-5">
            <button type="button" x-on:click="$dispatch('close-modal', 'detail-borrow')"
                    class="rounded-md bg-gray-300 px-6 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-400">
                Cancel
            </button>
        </div>
    </x-modal>
</div>