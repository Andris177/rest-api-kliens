<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class ActorController extends Controller
{
    /**
     * Lista + keresés
     */
    public function index(Request $request)
    {
        $needle = $request->get('needle');

        try {
            $query = [];
            if ($needle) {
                $query['needle'] = $needle;
            }

            $response = Http::api()->get('actors', $query);

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Ismeretlen hiba történt az API hívás során.';
                return back()->with('error', $message);
            }

            $actors = $response->json() ?? [];

            return view('actors.index', [
                'actors'          => $actors,
                'needle'          => $needle,
                'isAuthenticated' => $this->isAuthenticated(),
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Nem sikerült betölteni a listát: ' . $e->getMessage());
        }
    }

    /**
     * Új színész űrlap
     */
    public function create()
    {
        if (!$this->isAuthenticated()) {
            return redirect()
                ->route('actors.index')
                ->with('error', 'Az adatmódosításhoz be kell jelentkezni.');
        }

        return view('actors.create');
    }

    /**
     * Új színész mentése az API felé
     */
    public function store(Request $request)
    {
        if (!$this->isAuthenticated()) {
            return redirect()
                ->route('actors.index')
                ->with('error', 'Az adatmódosításhoz be kell jelentkezni.');
        }

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
                ->post('actors', $validated);

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült létrehozni a színészt.';
                return redirect()
                    ->route('actors.index')
                    ->with('error', $message);
            }

            return redirect()
                ->route('actors.index')
                ->with('success', 'Színész sikeresen létrehozva.');

        } catch (\Exception $e) {
            return redirect()
                ->route('actors.index')
                ->with('error', 'Hiba az API hívás során: ' . $e->getMessage());
        }
    }

    /**
     * Szerkesztő űrlap
     */
    public function edit($id)
    {
        if (!$this->isAuthenticated()) {
            return redirect()
                ->route('actors.index')
                ->with('error', 'Az adatmódosításhoz be kell jelentkezni.');
        }

        try {
            // Mivel az API-ban nincs show végpont, lehúzzuk a listát és keresünk
            $response = Http::api()->get('actors');

            if ($response->failed()) {
                return redirect()
                    ->route('actors.index')
                    ->with('error', 'Nem sikerült lekérdezni a színészeket.');
            }

            $actors = $response->json() ?? [];
            $actor = collect($actors)->firstWhere('id', (int)$id);

            if (!$actor) {
                return redirect()
                    ->route('actors.index')
                    ->with('error', 'A megadott színész nem található.');
            }

            return view('actors.edit', [
                'actor' => $actor,
            ]);

        } catch (\Exception $e) {
            return redirect()
                ->route('actors.index')
                ->with('error', 'Hiba történt: ' . $e->getMessage());
        }
    }

    /**
     * Színész módosítása
     */
    public function update(Request $request, $id)
    {
        if (!$this->isAuthenticated()) {
            return redirect()
                ->route('actors.index')
                ->with('error', 'Az adatmódosításhoz be kell jelentkezni.');
        }

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
                ->patch("actors/{$id}", $validated);

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült frissíteni a színészt.';
                return redirect()
                    ->route('actors.index')
                    ->with('error', $message);
            }

            return redirect()
                ->route('actors.index')
                ->with('success', 'Színész sikeresen frissítve.');

        } catch (\Exception $e) {
            return redirect()
                ->route('actors.index')
                ->with('error', 'Hiba az API hívás során: ' . $e->getMessage());
        }
    }

    /**
     * Színész törlése
     */
    public function destroy($id)
    {
        if (!$this->isAuthenticated()) {
            return redirect()
                ->route('actors.index')
                ->with('error', 'Az adatmódosításhoz be kell jelentkezni.');
        }

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->delete("actors/{$id}");

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült törölni a színészt.';
                return redirect()
                    ->route('actors.index')
                    ->with('error', $message);
            }

            return redirect()
                ->route('actors.index')
                ->with('success', 'Színész sikeresen törölve.');

        } catch (\Exception $e) {
            return redirect()
                ->route('actors.index')
                ->with('error', 'Hiba az API hívás során: ' . $e->getMessage());
        }
    }

    /**
     * CSV export
     */
    public function exportCsv(): StreamedResponse
    {
        try {
            $response = Http::api()->get('actors');

            if ($response->failed()) {
                return redirect()
                    ->route('actors.index')
                    ->with('error', 'Nem sikerült exportálni a színészeket.');
            }

            $actors = $response->json() ?? [];

            $headers = [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename="actors.csv"',
            ];

            $callback = function () use ($actors) {
                $handle = fopen('php://output', 'w');

                // Fejléc sor
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
            return redirect()
                ->route('actors.index')
                ->with('error', 'Hiba az exportálás során: ' . $e->getMessage());
        }
    }

    /**
     * PDF export
     */
    public function exportPdf()
    {
        try {
            $response = Http::api()->get('actors');

            if ($response->failed()) {
                return redirect()
                    ->route('actors.index')
                    ->with('error', 'Nem sikerült exportálni a színészeket PDF-be.');
            }

            $actors = $response->json() ?? [];

            $pdf = Pdf::loadView('actors.pdf', [
                'actors' => $actors,
            ]);

            return $pdf->download('actors.pdf');

        } catch (\Exception $e) {
            return redirect()
                ->route('actors.index')
                ->with('error', 'Hiba a PDF készítésekor: ' . $e->getMessage());
        }
    }
}
