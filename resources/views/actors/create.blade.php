<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Új színész</h2>
    </x-slot>

    <div class="p-6 max-w-3xl">
        @if ($errors->any())
            <div class="mb-4 border border-red-300 bg-red-50 p-4">
                <ul class="list-disc pl-5 text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('actors.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block font-medium mb-1">Név</label>
                <input type="text" name="name" class="border p-2 w-full" value="{{ old('name') }}" required>
            </div>

            <div>
                <label class="block font-medium mb-1">Leírás</label>
                <textarea name="description" class="border p-2 w-full" rows="4">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block font-medium mb-1">Születési dátum</label>
                <input type="date" name="birth_date" class="border p-2 w-full" value="{{ old('birth_date') }}">
            </div>

            <div>
                <label class="block font-medium mb-1">Nem</label>
                <select name="gender" class="border p-2 w-full" required>
                    <option value="">-- Válassz --</option>
                    <option value="férfi" {{ old('gender') === 'férfi' ? 'selected' : '' }}>Férfi</option>
                    <option value="nő" {{ old('gender') === 'nő' ? 'selected' : '' }}>Nő</option>
                </select>
            </div>

            <div>
                <label class="block font-medium mb-1">Kép (URL vagy fájlnév)</label>
                <input type="text" name="image" class="border p-2 w-full" value="{{ old('image') }}">
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-green-600 text-white px-4 py-2">Mentés</button>
                <a href="{{ route('actors.index') }}" class="bg-gray-600 text-white px-4 py-2">Mégse</a>
            </div>
        </form>
    </div>
</x-app-layout>
