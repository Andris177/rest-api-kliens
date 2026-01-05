<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Új kategória</h2>
    </x-slot>

    <div class="p-6">
        <form method="POST" action="{{ route('categories.store') }}">
            @csrf

            <div class="mb-4">
                <label>Név</label>
                <input type="text" name="name" class="border p-2 w-full" required>
            </div>

            <button class="bg-green-600 text-white px-4 py-2">Mentés</button>
        </form>
    </div>
</x-app-layout>
