<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;

class DirectorController extends Controller
{
    /**
     * Lista + keresés (publikus)
     */
    public function index(Request $request)
    {
        $needle = $request->get('needle');

        try {
            $query = [];
            if ($needle) {
                $query['needle'] = $needle;
            }

            $response = Http::api()->get('/directors', $query);

            if ($response->failed()) {
                return back()->with('error', 'Nem sikerült lekérdezni a rendezőket.');
            }

            $directors = $response->json() ?? [];

            return view('directors.index', [
                'directors' => $directors,
                'needle' => $needle,
                'isAuthenticated' => $this->isAuthenticated(),
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Új rendező űrlap (auth middleware védi)
     */
    public function create()
    {
        return view('directors.create');
    }

    /**
     * Új rendező mentése
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->post('/directors', $validated);

            if ($response->failed()) {
                return redirect()->route('directors.index')
                    ->with('error', 'Nem sikerült létrehozni a rendezőt.');
            }

            return redirect()->route('directors.index')
                ->with('success', 'Rendező sikeresen létrehozva.');
        } catch (\Exception $e) {
            return redirect()->route('directors.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Szerkesztő űrlap
     */
    public function edit($id)
    {
        try {
            // NOTE: nincs GET /directors/{id} végpont → listából választunk
            $response = Http::api()->get('/directors');

            if ($response->failed()) {
                return redirect()->route('directors.index')
                    ->with('error', 'Nem sikerült lekérdezni a rendezőket.');
            }

            $directors = $response->json() ?? [];
            $director = collect($directors)->firstWhere('id', (int)$id);

            if (!$director) {
                return redirect()->route('directors.index')
                    ->with('error', 'A rendező nem található.');
            }

            return view('directors.edit', ['director' => $director]);
        } catch (\Exception $e) {
            return redirect()->route('directors.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Rendező módosítása
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->patch("/directors/{$id}", $validated);

            if ($response->failed()) {
                return redirect()->route('directors.index')
                    ->with('error', 'Nem sikerült frissíteni a rendezőt.');
            }

            return redirect()->route('directors.index')
                ->with('success', 'Rendező sikeresen frissítve.');
        } catch (\Exception $e) {
            return redirect()->route('directors.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Rendező törlése
     */
    public function destroy($id)
    {
        try {
            $response = Http::api()
                ->withToken($this->token)
                ->delete("/directors/{$id}");

            if ($response->failed()) {
                return redirect()->route('directors.index')
                    ->with('error', 'Nem sikerült törölni a rendezőt.');
            }

            return redirect()->route('directors.index')
                ->with('success', 'Rendező sikeresen törölve.');
        } catch (\Exception $e) {
            return redirect()->route('directors.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * CSV export
     */
    public function exportCsv()
    {
        try {
            $response = Http::api()->get('/directors');

            if ($response->failed()) {
                return redirect()->route('directors.index')
                    ->with('error', 'Nem sikerült exportálni.');
            }

            $directors = $response->json() ?? [];

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="directors.csv"',
            ];

            $callback = function () use ($directors) {
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($handle, ['ID', 'Name']);

                foreach ($directors as $director) {
                    fputcsv($handle, [
                        $director['id'] ?? '',
                        $director['name'] ?? '',
                    ]);
                }

                fclose($handle);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return redirect()->route('directors.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * PDF export
     */
    public function exportPdf()
    {
        try {
            $response = Http::api()->get('/directors');

            if ($response->failed()) {
                return redirect()->route('directors.index')
                    ->with('error', 'Nem sikerült PDF-et készíteni.');
            }

            $directors = $response->json() ?? [];

            $pdf = Pdf::loadView('exports.directors_pdf', [
                'directors' => $directors,
            ]);

            return $pdf->download('directors.pdf');
        } catch (\Exception $e) {
            return redirect()->route('directors.index')
                ->with('error', $e->getMessage());
        }
    }
}
