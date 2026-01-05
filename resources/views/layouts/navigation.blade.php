<nav class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">

        {{-- Bal oldal --}}
        <div class="flex items-center gap-4">
            <a href="{{ url('/') }}" class="font-bold text-lg">
                {{ config('app.name', 'API Client') }}
            </a>

            <a href="{{ route('movies.index') }}" class="text-gray-700 hover:underline">
                Movies
            </a>
            <a href="{{ route('actors.index') }}" class="text-gray-700 hover:underline">
                Actors
            </a>
            <a href="{{ route('directors.index') }}" class="text-gray-700 hover:underline">
                Directors
            </a>
            <a href="{{ route('categories.index') }}" class="text-gray-700 hover:underline">
                Categories
            </a>
        </div>

        {{-- Jobb oldal --}}
        <div class="flex items-center gap-4">
            @if(session()->has('api_token'))
                <span class="text-gray-600">
                    {{ session('user_email') }}
                </span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-red-600 hover:underline">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-blue-600 hover:underline">
                    Login
                </a>
            @endif
        </div>

    </div>
</nav>
