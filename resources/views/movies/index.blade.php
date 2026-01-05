<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Filmek</h2>
    </x-slot>

    <div class="p-6 space-y-4">

        {{-- Keresés --}}
        <form method="GET" action="{{ route('movies.index') }}" class="flex gap-2 items-center">
            <input
                type="text"
                name="needle"
                value="{{ $needle ?? '' }}"
                class="border rounded p-2 w-64"
                placeholder="Keresés..."
            >
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Keresés</button>

            @if(!empty($needle))
                <a href="{{ route('movies.index') }}" class="px-4 py-2 rounded border">Törlés</a>
            @endif
        </form>

        {{-- Üzenetek --}}
        @if(session('success'))
            <div class="p-3 rounded bg-green-100 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-3 rounded bg-red-100 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        {{-- Gombok --}}
        <div class="flex gap-2">
            @if(!empty($isAuthenticated))
                <a href="{{ route('movies.create') }}" class="bg-green-600 text-white px-4 py-2 rounded">
                    Új film
                </a>
            @endif

            <a href="{{ route('movies.export.csv') }}" class="bg-gray-700 text-white px-4 py-2 rounded">
                CSV export
            </a>

            <a href="{{ route('movies.export.pdf') }}" class="bg-gray-700 text-white px-4 py-2 rounded">
                PDF export
            </a>
        </div>

        {{-- Táblázat --}}
        <div class="overflow-auto">
            <table class="w-full border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border p-2 text-left">ID</th>
                        <th class="border p-2 text-left">Cím</th>
                        <th class="border p-2 text-left">Director ID</th>
                        <th class="border p-2 text-left">Category ID</th>
                        @if(!empty($isAuthenticated))
                            <th class="border p-2 text-left">Műveletek</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($movies as $m)
                        <tr>
                            <td class="border p-2">{{ $m['id'] ?? '' }}</td>
                            <td class="border p-2">{{ $m['title'] ?? '' }}</td>
                            <td class="border p-2">{{ $m['director_id'] ?? '' }}</td>
                            <td class="border p-2">{{ $m['category_id'] ?? '' }}</td>

                            @if(!empty($isAuthenticated))
                                <td class="border p-2">
                                    @if(isset($m['id']))
                                        <div class="flex gap-3 items-center">
                                            <a href="{{ route('movies.edit', $m['id']) }}" class="text-blue-700 underline">
                                                Szerkesztés
                                            </a>

                                            <form method="POST" action="{{ route('movies.destroy', $m['id']) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="text-red-700 underline"
                                                    onclick="return confirm('Biztos törlöd?')"
                                                >
                                                    Törlés
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-gray-500">Nincs ID</span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td class="border p-2" colspan="{{ !empty($isAuthenticated) ? 5 : 4 }}">
                                Nincs találat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
