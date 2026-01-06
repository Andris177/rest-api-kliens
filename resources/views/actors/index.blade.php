<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            Színészek
        </h2>
    </x-slot>

    <div class="p-6 max-w-7xl mx-auto">
        @if (session('success'))
            <div class="mb-3 text-green-700 bg-green-100 border border-green-200 px-4 py-2 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-3 text-red-700 bg-red-100 border border-red-200 px-4 py-2 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex items-center justify-between mb-4">
            <form method="GET" action="{{ route('actors.index') }}" class="flex gap-2">
                <input type="text"
                       name="needle"
                       class="border rounded px-3 py-2"
                       placeholder="Keresés név alapján..."
                       value="{{ $needle }}">
                <button class="bg-blue-600 text-white px-4 py-2 rounded">
                    Keresés
                </button>
            </form>

            <div class="flex gap-2">
                <a href="{{ route('actors.export.csv') }}" class="bg-gray-600 text-white px-4 py-2 rounded">
                    CSV
                </a>
                <a href="{{ route('actors.export.pdf') }}" class="bg-gray-600 text-white px-4 py-2 rounded">
                    PDF
                </a>

                @if ($isAuthenticated)
                    <a href="{{ route('actors.create') }}" class="bg-green-600 text-white px-4 py-2 rounded">
                        Új színész
                    </a>
                @endif
            </div>
        </div>

        <table class="w-full border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">ID</th>
                    <th class="border p-2">Név</th>
                    <th class="border p-2">Születési dátum</th>
                    <th class="border p-2">Nem</th>
                    <th class="border p-2">Kép</th>
                    @if ($isAuthenticated)
                        <th class="border p-2">Műveletek</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($actors as $actor)
                    <tr>
                        <td class="border p-2">{{ $actor['id'] ?? '' }}</td>
                        <td class="border p-2">{{ $actor['name'] ?? '' }}</td>
                        <td class="border p-2">{{ $actor['birth_date'] ?? '' }}</td>
                        <td class="border p-2">{{ $actor['gender'] ?? '' }}</td>
                        <td class="border p-2">
                            @if (!empty($actor['image']))
                                <img src="{{ $actor['image'] }}" alt="{{ $actor['name'] }}" class="h-10">
                            @endif
                        </td>

                        @if ($isAuthenticated)
                            <td class="border p-2">
                                <div class="flex gap-3">
                                    <a href="{{ route('actors.edit', $actor['id']) }}" class="text-blue-600">
                                        Szerkesztés
                                    </a>

                                    <form action="{{ route('actors.destroy', $actor['id']) }}"
                                          method="POST"
                                          onsubmit="return confirm('Biztosan törlöd?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600">
                                            Törlés
                                        </button>
                                    </form>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isAuthenticated ? 6 : 5 }}" class="border p-4 text-center">
                            Nincs megjeleníthető színész.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
