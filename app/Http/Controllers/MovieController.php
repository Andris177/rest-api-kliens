<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class MovieController extends Controller
{
    public function index(Request $request)
    {
        $needle = $request->get('needle');

        $query = [];
        if ($needle) $query['needle'] = $needle;

        $response = Http::api()->get('movies', $query);

        if ($response->failed()) {
            return back()->with('error', $response->json('message') ?? 'API hiba');
        }

        $movies = $response->json('movies') ?? [];

        return view('movies.index', [
            'movies' => $movies,
            'needle' => $needle,
            'isAuthenticated' => $this->isAuthenticated(),
        ]);
    }

    public function create()
    {
        $categories = $this->fetchCategories();
        $directors  = $this->fetchDirectors();

        return view('movies.create', compact('categories', 'directors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer'],
            'director_id' => ['required', 'integer'],
        ]);

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->post('movies', $validated);

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült létrehozni a filmet.';
                return redirect()->route('movies.index')->with('error', $message);
            }

            return redirect()->route('movies.index')->with('success', 'Film sikeresen létrehozva.');
        } catch (\Exception $e) {
            return redirect()->route('movies.index')->with('error', 'Hiba az API hívás során: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $response = Http::api()->get('movies');

            if ($response->failed()) {
                return redirect()->route('movies.index')->with('error', 'Nem sikerült lekérdezni a filmeket.');
            }

            $movies = $response->json('movies') ?? [];
            $movie  = collect($movies)->firstWhere('id', (int)$id);

            if (!$movie) {
                return redirect()->route('movies.index')->with('error', 'A megadott film nem található.');
            }

            $categories = $this->fetchCategories();
            $directors  = $this->fetchDirectors();

            return view('movies.edit', compact('movie', 'categories', 'directors'));
        } catch (\Exception $e) {
            return redirect()->route('movies.index')->with('error', 'Hiba történt: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer'],
            'director_id' => ['required', 'integer'],
        ]);

        try {
            // API oldalon PATCH van, ezért itt PATCH maradjon
            $response = Http::api()
                ->withToken($this->token)
                ->patch("movies/{$id}", $validated);

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült frissíteni a filmet.';
                return redirect()->route('movies.index')->with('error', $message);
            }

            return redirect()->route('movies.index')->with('success', 'Film sikeresen frissítve.');
        } catch (\Exception $e) {
            return redirect()->route('movies.index')->with('error', 'Hiba az API hívás során: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $response = Http::api()
                ->withToken($this->token)
                ->delete("movies/{$id}");

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült törölni a filmet.';
                return redirect()->route('movies.index')->with('error', $message);
            }

            return redirect()->route('movies.index')->with('success', 'Film sikeresen törölve.');
        } catch (\Exception $e) {
            return redirect()->route('movies.index')->with('error', 'Hiba az API hívás során: ' . $e->getMessage());
        }
    }

    public function exportCsv(): StreamedResponse
    {
        try {
            $response = Http::api()->get('movies');

            if ($response->failed()) {
                return redirect()->route('movies.index')->with('error', 'Nem sikerült exportálni CSV-be.');
            }

            $movies = $response->json('movies') ?? [];

            $headers = [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="movies.csv"',
            ];

            $callback = function () use ($movies) {
                $handle = fopen('php://output', 'w');
                // BOM Excelhez
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

                fputcsv($handle, ['ID', 'Title', 'Description', 'Cover image', 'Category ID', 'Director ID']);

                foreach ($movies as $m) {
                    fputcsv($handle, [
                        $m['id'] ?? '',
                        $m['title'] ?? '',
                        $m['description'] ?? '',
                        $m['cover_image'] ?? '',
                        $m['category_id'] ?? '',
                        $m['director_id'] ?? '',
                    ]);
                }

                fclose($handle);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return redirect()->route('movies.index')->with('error', 'Hiba CSV exportnál: ' . $e->getMessage());
        }
    }

    public function exportPdf()
    {
        try {
            $response = Http::api()->get('movies');

            if ($response->failed()) {
                return redirect()->route('movies.index')->with('error', 'Nem sikerült exportálni PDF-be.');
            }

            $movies = $response->json('movies') ?? [];

            $pdf = Pdf::loadView('exports.movies_pdf', [
                'movies' => $movies,
            ]);

            return $pdf->download('movies.pdf');
        } catch (\Exception $e) {
            return redirect()->route('movies.index')->with('error', 'Hiba PDF exportnál: ' . $e->getMessage());
        }
    }

    private function fetchCategories(): array
    {
        try {
            $r = Http::api()->get('categories');
            if ($r->failed()) return [];
            return $r->json('categories') ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    private function fetchDirectors(): array
    {
        try {
            $r = Http::api()->get('directors');
            if ($r->failed()) return [];
            return $r->json('directors') ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }
}
