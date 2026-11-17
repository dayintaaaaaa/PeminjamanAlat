<x-modal name="detail-category" maxWidth="lg">
    <div class="border-b border-gray-300 px-6 py-4">
        <h2 class="text-base font-bold text-gray-900">Detail Category</h2>
    </div>

    <div class="px-6 text-sm">
        <div class="flex items-center gap-3 border-b border-gray-300 py-3">
            <span class="w-32 text-gray-700">Category Code</span><span>:</span>
            <span class="flex-1 text-gray-900" x-text="detail.code"></span>
        </div>
        <div class="flex items-center gap-3 border-b border-gray-300 py-3">
            <span class="w-32 text-gray-700">Category Name</span><span>:</span>
            <span class="flex-1 text-gray-900" x-text="detail.name"></span>
        </div>
        <div class="flex items-center gap-3 border-b border-gray-300 py-3">
            <span class="w-32 text-gray-700">Total Items</span><span>:</span>
            <span class="flex-1 text-gray-900" x-text="detail.total + ' Items'"></span>
        </div>
        <div class="flex items-start gap-3 border-b border-gray-300 py-3">
            <span class="w-32 text-gray-700">Description</span><span>:</span>
            <span class="flex-1 text-gray-900" x-text="detail.description"></span>
        </div>
    </div>

    <div class="flex justify-end px-6 py-4">
        <button type="button" x-on:click="$dispatch('close-modal', 'detail-category')"
                class="rounded-md bg-gray-300 px-6 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-400">
            Close
        </button>
    </div>
</x-modal>