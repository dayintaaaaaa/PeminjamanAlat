<x-app-layout>
    <x-slot name="header">
        <span class="font-normal text-gray-500">Master Data</span>
        <span class="font-normal text-gray-500">/</span>
        <a href="{{ route('master.tools-data') }}" class="font-normal text-gray-500 hover:text-gray-900">Tools Data</a>
        <span class="font-normal text-gray-500">/</span>
        Add Tools
    </x-slot>

    <div class="flex items-start justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold">Add Tools</h1>
            <p class="text-sm text-gray-500 mt-1">Fill in the details below to register a new equipment item into the system.</p>
        </div>
        <a href="{{ route('master.tools-data') }}"
           class="inline-flex items-center gap-2 bg-[var(--c-primary)] text-white text-sm font-semibold rounded-lg px-5 py-3 shadow hover:opacity-90">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <x-card>
        <form method="POST" action="{{ route('tools.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-2 gap-x-8">

                {{-- ===== KOLOM KIRI ===== --}}
                <div class="space-y-4" x-data="{ codes: @js($nextCodes), category: '{{ old('id_category_tools') }}' }">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-900 mb-1">Category</label>
                        <select id="category" name="id_category_tools" x-model="category" required class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm text-gray-900 focus:border-[var(--c-primary)] focus:ring-0">
                            <option value="" disabled @selected(! old('id_category_tools'))>Select category</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected((string) old('id_category_tools') === (string) $cat->id)>
                                    {{ $cat->category_code }} | {{ $cat->category_name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('id_category_tools')" class="mt-1" />
                    </div>

                    <div>
                        <label for="tool_code" class="block text-sm font-medium text-gray-900 mb-1">Tool Code</label>
                        <input type="text" id="tool_code" readonly placeholder="Auto-generated"  :value="codes[category] ?? ''"
                               class="w-full cursor-not-allowed rounded-lg border-2 border-[var(--c-accent)] bg-gray-100 px-3 py-2 text-sm text-gray-500 placeholder:text-gray-500 focus:border-[var(--c-accent)] focus:ring-0">
                    </div>

                    <div>
                        <label for="tool_name" class="block text-sm font-medium text-gray-900 mb-1">Tool Name</label>
                        <input type="text" id="tool_name" name="tools_name" value="{{ old('tools_name') }}" required
                               placeholder="Enter tool name.."
                               class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-[var(--c-primary)] focus:ring-0">
                        <x-input-error :messages="$errors->get('tools_name')" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="qty" class="block text-sm font-medium text-gray-900 mb-1">Total Qty</label>
                            <input type="number" id="qty" name="tools_qty" value="{{ old('tools_qty') }}" min="0" required placeholder="Qty"
                                   class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-[var(--c-primary)] focus:ring-0">
                            <x-input-error :messages="$errors->get('tools_qty')" class="mt-1" />
                        </div>
                        <div>
                            <label for="condition" class="block text-sm font-medium text-gray-900 mb-1">Condition</label>
                            <select id="condition" name="tools_condition" required
                                    class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm text-gray-900 focus:border-[var(--c-primary)] focus:ring-0">
                                <option value="" disabled @selected(! old('tools_condition'))>Select</option>
                                @foreach (['Good', 'Fair', 'Damaged'] as $c)
                                    <option value="{{ $c }}" @selected(old('tools_condition') === $c)>{{ $c }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('tools_condition')" class="mt-1" />
                        </div>
                    </div>
                </div>

                {{-- ===== KOLOM KANAN ===== --}}
                <div class="space-y-4">
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-900 mb-1">Storage Location</label>
                        <input type="text" id="location" name="storage_location" value="{{ old('storage_location') }}"
                               placeholder="e.g. Rack A - Shelf 2"
                               class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-[var(--c-primary)] focus:ring-0">
                    </div>

                    {{-- upload foto --}}
                    <div x-data="{
                            dragging: false,
                            preview: null,
                            fileName: '',
                            handle(file) {
                                if (!file) return;
                                this.fileName = file.name;
                                this.preview = URL.createObjectURL(file);
                            }
                         }">
                        <span class="block text-sm font-medium text-gray-900 mb-1">Tool Image</span>

                        <label for="tool_image"
                               @dragover.prevent="dragging = true"
                               @dragleave.prevent="dragging = false"
                               @drop.prevent="dragging = false; $refs.file.files = $event.dataTransfer.files; handle($refs.file.files[0])"
                               :class="dragging ? 'bg-emerald-50' : 'bg-white'"
                               class="flex h-24 cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-[var(--c-accent)] text-center text-xs text-gray-700">

                            <div x-show="!preview">
                                <i class="bi bi-camera text-xl text-gray-500"></i>
                                <p class="mt-1">Drag &amp; Drop Image or <span class="font-bold">Browse File</span></p>
                            </div>

                            <div x-show="preview" x-cloak class="flex items-center gap-3 px-3">
                                <img :src="preview" alt="" class="h-16 w-16 rounded object-cover">
                                <span class="truncate" x-text="fileName"></span>
                            </div>

                            <input type="file" id="tool_image" name="photo" accept="image/*" class="hidden"
                                   x-ref="file" @change="handle($event.target.files[0])">
                        </label>
                        <x-input-error :messages="$errors->get('photo')" class="mt-1" />
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-900 mb-1">Description (optional)</label>
                        <textarea id="description" name="description" rows="3" placeholder="Enter description.."
                                  class="w-full rounded-lg border-2 border-[var(--c-accent)] bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:border-[var(--c-primary)] focus:ring-0">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-5">
                <button type="submit"
                        class="rounded-lg bg-[var(--c-primary)] px-8 py-2.5 text-sm font-semibold text-white shadow hover:opacity-90">
                    Save
                </button>
            </div>
        </form>
    </x-card>
</x-app-layout>