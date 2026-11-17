<x-app-layout>
    <x-slot name="header">
        <a href="/users" class="font-normal text-gray-500 hover:text-gray-900">User Management</a>
        <span class="font-normal text-gray-500">/</span>
        Update User
    </x-slot>

    {{-- judul + tombol Back --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Update User</h1>
            <p class="text-sm text-gray-500 mt-1">Update user profile and account permissions.</p>
        </div>
        <a href="/users"
            class="inline-flex items-center gap-2 bg-[var(--c-primary)] text-white text-sm font-semibold rounded-lg px-5 py-3 shadow hover:opacity-90">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <x-card>
        <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Full Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-900 mb-2">Full Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                    placeholder="Enter your full name.."
                    class="w-full rounded-xl border-2 border-[var(--c-accent)] bg-white px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 focus:border-[var(--c-primary)] focus:ring-0">
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-900 mb-2">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                    placeholder="Enter your email.."
                    class="w-full rounded-xl border-2 border-[var(--c-accent)] bg-white px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 focus:border-[var(--c-primary)] focus:ring-0">
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            {{-- Password + Role (sebaris) --}}
            <div class="grid grid-cols-3 gap-4">
                <div x-data="{ show: false }" class="col-span-2">
                    <label for="password" class="block text-sm font-medium text-gray-900 mb-2">Password</label>
                    <div class="relative">
                        <input id="password" name="password" :type="show ? 'text' : 'password'"
                            placeholder="Keep current.."
                            class="w-full rounded-xl border-2 border-[var(--c-accent)] bg-white px-4 py-3 pr-11 text-sm text-gray-900 placeholder:text-gray-400 focus:border-[var(--c-primary)] focus:ring-0">
                        <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700"
                            aria-label="Show or hide password">
                            <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-900 mb-2">Role</label>
                    <select id="role" name="role" required class="w-full rounded-xl border-2 border-[var(--c-accent)] bg-white px-4 py-3 text-sm text-gray-900 focus:border-[var(--c-primary)] focus:ring-0">
                        @foreach ($roles as $role)
                        <option value="{{ $role }}" @selected(old('role', $user->getRoleNames()->first()) === $role)>
                            {{ ucfirst($role) }}
                        </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-1" />
                </div>
            </div>

            {{-- Tombol Update --}}
            <div class="flex justify-end pt-2">
                <button type="submit"
                    class="rounded-lg bg-[var(--c-primary)] px-8 py-3 text-sm font-semibold text-white shadow hover:opacity-90">
                    Update
                </button>
            </div>
        </form>
    </x-card>
</x-app-layout>