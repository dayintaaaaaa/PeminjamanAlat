<x-app-layout>
    <x-slot name="header">
        <a href="/users" class="font-normal text-gray-500 hover:text-gray-900">User Management</a>
        <span class="font-normal text-gray-500">/</span>
        Add User
    </x-slot>

    {{-- buat judul sama sub judul + button --}}
    <div class="flex items-start justify-between mb-8">
        <div>
            <h1 class="font-bold text-2xl">Add User</h1>
            <p class="text-sm text-gray-500 mt-1">Create a new user account adn assign roles</p>
        </div>
        <a href="/users" class="bg-[var(--c-primary)] font-semibold text-sm text-white text-center rounded-lg px-6 py-2.5 shadow hover:opacity-90" title="Back"><i class="bi bi-arrow-left"></i> Back</a>
    </div>

    <x-card>
        <form method="POST" action="{{ route('users.store') }}" class="space-y-5">
            @csrf

            {{-- Full Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-900 mb-2">Full Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                    placeholder="Enter your full name.."
                    class="w-full rounded-xl border-2 border-[var(--c-accent)] bg-white px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 focus:border-[var(--c-primary)] focus:ring-0">
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-900 mb-2">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                    placeholder="Enter your email.."
                    class="w-full rounded-xl border-2 border-[var(--c-accent)] bg-white px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 focus:border-[var(--c-primary)] focus:ring-0">
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            {{-- Password + Role (sebaris) --}}
            <div class="grid grid-cols-3 gap-4">
                {{-- Password: lebar 2 kolom --}}
                <div x-data="{ show: false }" class="col-span-2">
                    <label for="password" class="block text-sm font-medium text-gray-900 mb-2">Password</label>
                    <div class="relative">
                        <input id="password" name="password" :type="show ? 'text' : 'password'" required
                            placeholder="Enter password.."
                            class="w-full rounded-xl border-2 border-[var(--c-accent)] bg-white px-4 py-3 pr-11 text-sm text-gray-900 placeholder:text-gray-400 focus:border-[var(--c-primary)] focus:ring-0">
                        <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700"
                            aria-label="Show or hide password">
                            <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                {{-- Role: lebar 1 kolom --}}
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-900 mb-2">Role</label>
                    <select id="role" name="role" required class="...class lamamu...">
                        <option value="" disabled @selected(! old('role'))>Select role</option>
                        @foreach ($roles as $role)
                        <option value="{{ $role }}" @selected(old('role')===$role)>{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-1" />
                </div>
            </div>

            {{-- Tombol Save --}}
            <div class="flex justify-end pt-2">
                <button type="submit"
                    class="rounded-lg bg-[var(--c-primary)] px-8 py-3 text-sm font-semibold text-white shadow hover:opacity-90">
                    Save
                </button>
            </div>
        </form>
    </x-card>
</x-app-layout>