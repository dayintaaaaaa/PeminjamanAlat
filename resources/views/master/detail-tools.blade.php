<x-modal name="detail-tool" maxWidth="2xl">
    {{-- judul --}}
    <div class="border-b border-gray-300 px-6 py-4">
        <h2 class="text-base font-bold text-gray-900">Detail Tools</h2>
    </div>

    <div class="px-6 py-4 text-sm">

        {{-- bagian atas: foto + info utama --}}
        <div class="flex gap-5">
            {{-- foto alat: tampil kalau ada, ikon kamera kalau tidak --}}
            <div class="flex h-36 w-44 shrink-0 items-center justify-center overflow-hidden rounded bg-gray-300">
                <template x-if="detail.photo">
                    <img :src="detail.photo" alt="" class="h-full w-full object-cover">
                </template>
                <template x-if="!detail.photo">
                    <i class="bi bi-camera text-5xl text-gray-500"></i>
                </template>
            </div>

            {{-- info utama --}}
            <div class="flex-1">
                <div class="flex gap-3 border-b border-gray-300 py-2">
                    <span class="w-24 text-gray-700">Tool</span>
                    <span>:</span>
                    <span class="flex-1 text-gray-900" x-text="detail.tool"></span>
                </div>
                <div class="flex gap-3 border-b border-gray-300 py-2">
                    <span class="w-24 text-gray-700">Category</span>
                    <span>:</span>
                    <span class="flex-1 text-gray-900" x-text="detail.category"></span>
                </div>
                <div class="flex gap-3 border-b border-gray-300 py-2">
                    <span class="w-24 text-gray-700">Condition</span>
                    <span>:</span>
                    <span class="flex-1 text-gray-900" x-text="detail.condition"></span>
                </div>
                <div class="flex gap-3 border-b border-gray-300 py-2">
                    <span class="w-24 text-gray-700">Total Stock</span>
                    <span>:</span>
                    <span class="flex-1 text-gray-900" x-text="detail.total + ' Items'"></span>
                </div>
                <div class="flex gap-3 border-b border-gray-300 py-2">
                    <span class="w-24 text-gray-700">Available</span>
                    <span>:</span>
                    <span class="flex-1 text-gray-900" x-text="detail.available + ' Items'"></span>
                </div>
            </div>
        </div>

        {{-- bagian bawah: selebar penuh --}}
        <div class="mt-1">
            <div class="flex gap-3 border-b border-gray-300 py-2">
                <span class="w-24 text-gray-700">Location</span>
                <span>:</span>
                <span class="flex-1 text-gray-900" x-text="detail.location"></span>
            </div>
            <div class="flex gap-3 border-b border-gray-300 py-2">
                <span class="w-24 text-gray-700">Description</span>
                <span>:</span>
                <span class="flex-1 text-gray-900" x-text="detail.description"></span>
            </div>
            <div class="flex gap-3 border-b border-gray-300 py-2">
                <span class="w-24 text-gray-700">Create At</span>
                <span>:</span>
                <span class="flex-1 text-gray-900" x-text="detail.created"></span>
            </div>
            <div class="flex gap-3 border-b border-gray-300 py-2">
                <span class="w-24 text-gray-700">Update At</span>
                <span>:</span>
                <span class="flex-1 text-gray-900" x-text="detail.updated"></span>
            </div>
        </div>
    </div>

    {{-- tombol --}}
    <div class="flex justify-end px-6 pb-4">
        <button type="button" x-on:click="$dispatch('close-modal', 'detail-tool')"
                class="rounded-md bg-gray-300 px-6 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-400">
            Close
        </button>
    </div>
</x-modal>