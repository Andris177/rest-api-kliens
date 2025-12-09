@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-4">

    @if (session('success'))
        <div class="alert alert-success mb-3">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger mb-3">
            {{ session('error') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Színészek</h1>

        <div class="btn-group">
            <a href="{{ route('actors.export.csv') }}" class="btn btn-outline-secondary btn-sm">
                CSV export
            </a>
            <a href="{{ route('actors.export.pdf') }}" class="btn btn-outline-secondary btn-sm">
                PDF export
            </a>
            @if ($isAuthenticated)
                <a href="{{ route('actors.create') }}" class="btn btn-primary btn-sm">
                    Új színész
                </a>
            @endif
        </div>
    </div>

    <form method="GET" action="{{ route('actors.index') }}" class="mb-3">
        <div class="input-group">
            <input type="text"
                   name="needle"
                   class="form-control"
                   placeholder="Keresés név alapján..."
                   value="{{ $needle }}">
            <button class="btn btn-outline-primary" type="submit">Keresés</button>
        </div>
    </form>

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Név</th>
                <th>Születési dátum</th>
                <th>Nem</th>
                <th>Kép</th>
                @if ($isAuthenticated)
                    <th>Műveletek</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($actors as $actor)
                <tr>
                    <td>{{ $actor['id'] ?? '' }}</td>
                    <td>{{ $actor['name'] ?? '' }}</td>
                    <td>{{ $actor['birth_date'] ?? '' }}</td>
                    <td>{{ $actor['gender'] ?? '' }}</td>
                    <td>
                        @if (!empty($actor['image']))
                            <img src="{{ $actor['image'] }}" alt="{{ $actor['name'] }}" style="height:40px;">
                        @endif
                    </td>
                    @if ($isAuthenticated)
                        <td>
                            <a href="{{ route('actors.edit', $actor['id']) }}"
                               class="btn btn-sm btn-warning">
                                Szerkesztés
                            </a>
                            <form action="{{ route('actors.destroy', $actor['id']) }}"
                                  method="POST"
                                  style="display:inline-block;"
                                  onsubmit="return confirm('Biztosan törlöd?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" type="submit">
                                    Törlés
                                </button>
                            </form>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $isAuthenticated ? 6 : 5 }}" class="text-center">
                        Nincs megjeleníthető színész.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
