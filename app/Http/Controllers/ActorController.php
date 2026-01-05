<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;

class ActorController extends Controller
{
    /**
     * Lista + keresés (nyitott)
     */
    public function index(Request $request)
    {
        $needle = $request->get('needle');

        try {
            $query = [];
            if ($needle) {
                $query['needle'] = $needle;
            }

            $response = Http::api()->get('/actors', $query);

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Ismeretlen hiba történt az API hívás során.';
                return back()->with('error', $message);
            }

            $actors = $response->json() ?? [];

            return view('actors.index', [
                'actors' => $actors,
                'needle' => $needle,
                'isAuthenticated' => $this->isAuthenticated(),
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Nem sikerült betölteni a listát: ' . $e->getMessage());
        }
    }

    /**
     * Új színész űrlap (auth middleware védi)
     */
    public function create()
    {
        return view('actors.create');
    }

    /**
     * Új színész mentése (auth middleware védi)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'birth_date'  => ['nullable', 'date'],
            'gender'      => ['required', 'in:male,female,other'],
            'image'       => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->post('/actors', $validated);

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült létrehozni a színészt.';
                return redirect()->route('actors.index')->with('error', $message);
            }

            return redirect()->route('actors.index')->with('success', 'Színész sikeresen létrehozva.');
        } catch (\Exception $e) {
            return redirect()->route('actors.index')->with('error', 'Hiba az API hívás során: ' . $e->getMessage());
        }
    }

    /**
     * Szerkesztő űrlap (auth middleware védi)
     */
    public function edit($id)
    {
        try {
            // NOTE: Az API jelenleg nem biztosít GET /actors/{id} végpontot,
            // ezért a teljes lista lekéréséből választjuk ki a kért elemet.
            $response = Http::api()->get('/actors');

            if ($response->failed()) {
                return redirect()->route('actors.index')->with('error', 'Nem sikerült lekérdezni a színészeket.');
            }

            $actors = $response->json() ?? [];
            $actor  = collect($actors)->firstWhere('id', (int) $id);

            if (!$actor) {
                return redirect()->route('actors.index')->with('error', 'A megadott színész nem található.');
            }

            return view('actors.edit', ['actor' => $actor]);
        } catch (\Exception $e) {
            return redirect()->route('actors.index')->with('error', 'Hiba történt: ' . $e->getMessage());
        }
    }

    /**
     * Színész módosítása (auth middleware védi)
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'birth_date'  => ['nullable', 'date'],
            'gender'      => ['required', 'in:male,female,other'],
            'image'       => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->patch("/actors/{$id}", $validated);

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült frissíteni a színészt.';
                return redirect()->route('actors.index')->with('error', $message);
            }

            return redirect()->route('actors.index')->with('success', 'Színész sikeresen frissítve.');
        } catch (\Exception $e) {
            return redirect()->route('actors.index')->with('error', 'Hiba az API hívás során: ' . $e->getMessage());
        }
    }

    /**
     * Színész törlése (auth middleware védi)
     */
    public function destroy($id)
    {
        try {
            $response = Http::api()
                ->withToken($this->token)
                ->delete("/actors/{$id}");

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült törölni a színészt.';
                return redirect()->route('actors.index')->with('error', $message);
            }

            return redirect()->route('actors.index')->with('success', 'Színész sikeresen törölve.');
        } catch (\Exception $e) {
            return redirect()->route('actors.index')->with('error', 'Hiba az API hívás során: ' . $e->getMessage());
        }
    }

    /**
     * CSV export (auth middleware védi)
     */
    public function exportCsv()
    {
        try {
            $response = Http::api()->get('/actors');

            if ($response->failed()) {
                return redirect()->route('actors.index')->with('error', 'Nem sikerült exportálni a színészeket.');
            }

            $actors = $response->json() ?? [];

            $headers = [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="actors.csv"',
            ];

            $callback = function () use ($actors) {
                $handle = fopen('php://output', 'w');

                // Excel miatt BOM (opcionális, de hasznos)
                fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

                fputcsv($handle, ['ID', 'Name', 'Birth date', 'Gender', 'Description']);

                foreach ($actors as $actor) {
                    fputcsv($handle, [
                        $actor['id'] ?? '',
                        $actor['name'] ?? '',
                        $actor['birth_date'] ?? '',
                        $actor['gender'] ?? '',
                        $actor['description'] ?? '',
                    ]);
                }

                fclose($handle);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return redirect()->route('actors.index')->with('error', 'Hiba az exportálás során: ' . $e->getMessage());
        }
    }

    /**
     * PDF export (auth middleware védi)
     */
    public function exportPdf()
    {
        try {
            $response = Http::api()->get('/actors');

            if ($response->failed()) {
                return redirect()->route('actors.index')->with('error', 'Nem sikerült exportálni a színészeket PDF-be.');
            }

            $actors = $response->json() ?? [];

            $pdf = Pdf::loadView('exports.actors_pdf', [
                'actors' => $actors,
            ]);

            return $pdf->download('actors.pdf');
        } catch (\Exception $e) {
            return redirect()->route('actors.index')->with('error', 'Hiba a PDF készítésekor: ' . $e->getMessage());
        }
    }
}
