<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Kategória szerkesztése</h2>
    </x-slot>

    <div class="p-6">
        <form method="POST" action="{{ route('categories.update', $category['id']) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label>Név</label>
                <input type="text"
                       name="name"
                       class="border p-2 w-full"
                       value="{{ $category['name'] }}"
                       required>
            </div>

            <button class="bg-blue-600 text-white px-4 py-2">Frissítés</button>
        </form>
    </div>
</x-app-layout>
