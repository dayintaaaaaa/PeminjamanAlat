<x-alert-modal name="confirm-logout" type="confirm" title="Log out" message="Are you sure you want to log out?">
    <button type="button" x-on:click="$dispatch('close-modal', 'confirm-logout')"
            class="rounded-md bg-gray-200 px-6 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-300">
        Cancel
    </button>
    <button type="submit" form="logout-form"
            class="rounded-md bg-red-600 px-6 py-1.5 text-xs font-semibold text-white hover:bg-red-700">
        Log Out
    </button>
</x-alert-modal>

{{-- form tersembunyi yang benar-benar menjalankan logout --}}
<form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
    @csrf
</form>