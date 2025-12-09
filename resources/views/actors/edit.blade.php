@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-4">
    <h1>Színész szerkesztése</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('actors.update', $actor['id']) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Név</label>
            <input type="text" name="name" class="form-control"
                   value="{{ old('name', $actor['name'] ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Leírás</label>
            <textarea name="description" class="form-control">{{ old('description', $actor['description'] ?? '') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Születési dátum</label>
            <input type="date" name="birth_date" class="form-control"
                   value="{{ old('birth_date', $actor['birth_date'] ?? '') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Nem</label>
            <select name="gender" class="form-select" required>
                @php $gender = old('gender', $actor['gender'] ?? ''); @endphp
                <option value="male" {{ $gender === 'male' ? 'selected' : '' }}>Férfi</option>
                <option value="female" {{ $gender === 'female' ? 'selected' : '' }}>Nő</option>
                <option value="other" {{ $gender === 'other' ? 'selected' : '' }}>Egyéb</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Kép (URL vagy fájlnév)</label>
            <input type="text" name="image" class="form-control"
                   value="{{ old('image', $actor['image'] ?? '') }}">
        </div>

        <button type="submit" class="btn btn-primary">Mentés</button>
        <a href="{{ route('actors.index') }}" class="btn btn-secondary">Mégse</a>
    </form>
</div>
@endsection
