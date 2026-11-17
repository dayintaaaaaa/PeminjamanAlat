<x-guest-layout>
    <div class="w-full max-w-sm bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-center text-xl font-bold text-gray-900">Log In</h2>
        <p class="text-center text-xs text-gray-400 mb-6">Sign in to manage and borrow equipment</p>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" x-data="{ show: false }">
            @csrf

            <!--email-->
            <label for="email" class="text-xs font-medium">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                   placeholder="Enter email..."
                   class="w-full mt-1 rounded-lg border-2 border-[var(--c-accent)] focus:border-[var(--c-primary)] focus:ring-0 text-sm">
            <x-input-error :messages="$errors->get('email')" class="mt-1" />

            <!--password-->
            <label for="password" class="text-xs font-medium block mt-4">Password</label>
            <div class="relative mt-1">
                <input id="password" name="password" :type="show ? 'text' : 'password'" required
                       placeholder="Enter password..."
                       class="w-full rounded-lg border-2 border-[var(--c-accent)] focus:border-[var(--c-primary)] focus:ring-0 text-sm pr-10">
                <button type="button" @click="show = !show" class="absolute right-3 top-2.5 text-gray-400">
                    <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />

            <!--button submit-->
            <div class="flex justify-center mt-6">
                <button type="submit" class="bg-[var(--c-primary)] text-white text-sm font-semibold rounded-full px-12 py-2 hover:opacity-90">
                    Log In
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
