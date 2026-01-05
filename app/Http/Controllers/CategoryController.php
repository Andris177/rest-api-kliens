<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $needle = $request->get('needle');

        try {
            $query = [];
            if ($needle) {
                $query['needle'] = $needle;
            }

            $response = Http::api()->get('categories', $query);

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Ismeretlen hiba történt az API hívás során.';
                return back()->with('error', $message);
            }

            $categories = $response->json() ?? [];

            return view('categories.index', [
                'categories'      => $categories,
                'needle'          => $needle,
                'isAuthenticated' => $this->isAuthenticated(),
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Nem sikerült betölteni a kategóriákat: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->post('categories', $validated);

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült létrehozni a kategóriát.';
                return redirect()->route('categories.index')->with('error', $message);
            }

            return redirect()->route('categories.index')->with('success', 'Kategória sikeresen létrehozva.');
        } catch (\Exception $e) {
            return redirect()->route('categories.index')->with('error', 'Hiba az API hívás során: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            // nincs show endpoint? akkor listából keressük
            $response = Http::api()->get('categories');

            if ($response->failed()) {
                return redirect()->route('categories.index')->with('error', 'Nem sikerült lekérdezni a kategóriákat.');
            }

            $categories = $response->json() ?? [];
            $category = collect($categories)->firstWhere('id', (int)$id);

            if (!$category) {
                return redirect()->route('categories.index')->with('error', 'A megadott kategória nem található.');
            }

            return view('categories.edit', [
                'category' => $category,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('categories.index')->with('error', 'Hiba történt: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        try {
            // API-dban update PUT-ot használ? te döntöd el
            // nálad: CategoryController-ben PUT volt, de ha PATCH route-od van, akkor patch()
            $response = Http::api()
                ->withToken($this->token)
                ->put("categories/{$id}", $validated);

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült frissíteni a kategóriát.';
                return redirect()->route('categories.index')->with('error', $message);
            }

            return redirect()->route('categories.index')->with('success', 'Kategória sikeresen frissítve.');
        } catch (\Exception $e) {
            return redirect()->route('categories.index')->with('error', 'Hiba az API hívás során: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $response = Http::api()
                ->withToken($this->token)
                ->delete("categories/{$id}");

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült törölni a kategóriát.';
                return redirect()->route('categories.index')->with('error', $message);
            }

            return redirect()->route('categories.index')->with('success', 'Kategória sikeresen törölve.');
        } catch (\Exception $e) {
            return redirect()->route('categories.index')->with('error', 'Hiba az API hívás során: ' . $e->getMessage());
        }
    }

    public function exportCsv(): StreamedResponse
    {
        try {
            $response = Http::api()->get('categories');

            if ($response->failed()) {
                return redirect()->route('categories.index')->with('error', 'Nem sikerült exportálni CSV-be.');
            }

            $categories = $response->json() ?? [];

            $headers = [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename="categories.csv"',
            ];

            $callback = function () use ($categories) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['ID', 'Name']);

                foreach ($categories as $c) {
                    fputcsv($handle, [
                        $c['id'] ?? '',
                        $c['name'] ?? '',
                    ]);
                }

                fclose($handle);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return redirect()->route('categories.index')->with('error', 'Hiba CSV exportnál: ' . $e->getMessage());
        }
    }

    public function exportPdf()
    {
        try {
            $response = Http::api()->get('categories');

            if ($response->failed()) {
                return redirect()->route('categories.index')->with('error', 'Nem sikerült exportálni PDF-be.');
            }

            $categories = $response->json() ?? [];

            $pdf = Pdf::loadView('exports.categories_pdf', [
                'categories' => $categories,
            ]);

            return $pdf->download('categories.pdf');
        } catch (\Exception $e) {
            return redirect()->route('categories.index')->with('error', 'Hiba PDF exportnál: ' . $e->getMessage());
        }
    }
}
