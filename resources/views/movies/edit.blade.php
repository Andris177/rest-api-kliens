<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Film szerkesztése</h2>
    </x-slot>

    <div class="p-6 max-w-2xl space-y-4">

        @if($errors->any())
            <div class="p-3 rounded bg-red-100 text-red-800">
                <div class="font-semibold mb-2">Hibák:</div>
                <ul class="list-disc ml-5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('movies.update', $movie['id'] ?? 0) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-medium mb-1">ID</label>
                <input type="text" class="border rounded p-2 w-full bg-gray-100" value="{{ $movie['id'] ?? '' }}" disabled>
            </div>

            <div>
                <label class="block font-medium mb-1">Cím *</label>
                <input
                    type="text"
                    name="title"
                    value="{{ old('title', $movie['title'] ?? '') }}"
                    class="border rounded p-2 w-full"
                    required
                >
            </div>

            <div>
                <label class="block font-medium mb-1">Leírás</label>
                <textarea name="description" class="border rounded p-2 w-full" rows="5">{{ old('description', $movie['description'] ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium mb-1">Director ID *</label>
                    <input
                        type="number"
                        name="director_id"
                        value="{{ old('director_id', $movie['director_id'] ?? '') }}"
                        class="border rounded p-2 w-full"
                        required
                    >
                </div>

                <div>
                    <label class="block font-medium mb-1">Category ID *</label>
                    <input
                        type="number"
                        name="category_id"
                        value="{{ old('category_id', $movie['category_id'] ?? '') }}"
                        class="border rounded p-2 w-full"
                        required
                    >
                </div>
            </div>

            <div>
                <label class="block font-medium mb-1">Cover image (szöveg / útvonal)</label>
                <input
                    type="text"
                    name="cover_image"
                    value="{{ old('cover_image', $movie['cover_image'] ?? '') }}"
                    class="border rounded p-2 w-full"
                    placeholder="pl. films/film_1.jpg"
                >
            </div>

            <div class="flex gap-2">
                <button class="bg-blue-600 text-white px-4 py-2 rounded" type="submit">
                    Mentés
                </button>

                <a href="{{ route('movies.index') }}" class="border px-4 py-2 rounded">
                    Vissza
                </a>
            </div>
        </form>

    </div>
</x-app-layout>
