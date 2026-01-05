<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Kategóriák</h2>
    </x-slot>

    <div class="p-6">
        <form method="GET" class="mb-4 flex gap-2">
            <input type="text" name="needle" value="{{ $needle }}"
                   class="border p-2"
                   placeholder="Keresés név szerint">
            <button class="bg-blue-600 text-white px-4">Keresés</button>
        </form>

        @if(session('success'))
            <div class="text-green-600 mb-2">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="text-red-600 mb-2">{{ session('error') }}</div>
        @endif

        <div class="mb-4 flex gap-2">
            @if($isAuthenticated)
                <a href="{{ route('categories.create') }}"
                   class="bg-green-600 text-white px-4 py-2">
                    Új kategória
                </a>
            @endif

            <a href="{{ route('categories.export.csv') }}"
               class="bg-gray-600 text-white px-4 py-2">CSV</a>

            <a href="{{ route('categories.export.pdf') }}"
               class="bg-gray-600 text-white px-4 py-2">PDF</a>
        </div>

        <table class="w-full border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">ID</th>
                    <th class="border p-2">Név</th>
                    @if($isAuthenticated)
                        <th class="border p-2">Műveletek</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $c)
                    <tr>
                        <td class="border p-2">{{ $c['id'] }}</td>
                        <td class="border p-2">{{ $c['name'] }}</td>

                        @if($isAuthenticated)
                            <td class="border p-2 flex gap-2">
                                <a href="{{ route('categories.edit', $c['id']) }}"
                                   class="text-blue-600">Szerkesztés</a>

                                <form method="POST" action="{{ route('categories.destroy', $c['id']) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600"
                                            onclick="return confirm('Biztos törlöd?')">
                                        Törlés
                                    </button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
