<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Rendező szerkesztése</h2>
    </x-slot>

    <div class="p-6">
        <form method="POST" action="{{ route('directors.update', $director['id']) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label>Név</label>
                <input type="text"
                       name="name"
                       class="border p-2 w-full"
                       value="{{ $director['name'] }}"
                       required>
            </div>

            <button class="bg-blue-600 text-white px-4 py-2">
                Frissítés
            </button>
        </form>
    </div>
</x-app-layout>
