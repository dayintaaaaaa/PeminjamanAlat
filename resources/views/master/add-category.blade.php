<x-modal name="add-category" :show="$errors->addCategory->isNotEmpty()" maxWidth="lg">
    <form method="POST" action="{{ route('tool-categories.store') }}">
        @csrf

        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-base font-bold text-gray-900">Add Category</h2>
        </div>

        <div class="space-y-4 px-6 py-5 text-sm">
            <div>
                <div class="flex items-center gap-3">
                    <label for="add_code" class="w-32 text-gray-700">Category Code</label>
                    <span>:</span>
                    <input type="text" id="add_code" name="code" value="{{ old('code') }}" required
                           placeholder="Enter code category.."
                           class="flex-1 rounded-lg border-2 border-[var(--c-accent)] bg-white px-4 py-2 text-sm placeholder:text-gray-400 focus:border-[var(--c-primary)] focus:ring-0">
                </div>
                <x-input-error :messages="$errors->addCategory->get('code')" class="mt-1 ml-[8.5rem]" />
            </div>

            <div>
                <div class="flex items-center gap-3">
                    <label for="add_name" class="w-32 text-gray-700">Category Name</label>
                    <span>:</span>
                    <input type="text" id="add_name" name="name" value="{{ old('name') }}" required
                           placeholder="Enter category name.."
                           class="flex-1 rounded-lg border-2 border-[var(--c-accent)] bg-white px-4 py-2 text-sm placeholder:text-gray-400 focus:border-[var(--c-primary)] focus:ring-0">
                </div>
                <x-input-error :messages="$errors->addCategory->get('name')" class="mt-1 ml-[8.5rem]" />
            </div>

            <div class="flex items-start gap-3">
                <label for="add_description" class="w-32 pt-2 text-gray-700">Description</label>
                <span class="pt-2">:</span>
                <textarea id="add_description" name="description" rows="4"
                          placeholder="Enter description (optional)"
                          class="flex-1 rounded-lg border-2 border-[var(--c-accent)] bg-white px-4 py-2 text-sm placeholder:text-gray-400 focus:border-[var(--c-primary)] focus:ring-0">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-between px-6 pb-5">
            <button type="button" x-on:click="$dispatch('close-modal', 'add-category')"
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