<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class ActorController extends Controller
{
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

            // ✅ FONTOS: wrapperből a listát!
            $actors = $response->json('actors') ?? [];

            return view('actors.index', [
                'actors'          => $actors,
                'needle'          => $needle,
                'isAuthenticated' => $this->isAuthenticated(),
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Nem sikerült betölteni a listát: ' . $e->getMessage());
        }
    }

    public function create()
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('actors.index')->with('error', 'Az adatmódosításhoz be kell jelentkezni.');
        }

        return view('actors.create');
    }

    public function store(Request $request)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('actors.index')->with('error', 'Az adatmódosításhoz be kell jelentkezni.');
        }

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'birth_date'  => ['nullable', 'date'],
            'gender'      => ['nullable', 'in:férfi,nő'],
            'image'       => ['nullable', 'string', 'max:255'],
        ]);

        $response = Http::api()->withToken($this->token)->post('actors', $validated);

        if ($response->failed()) {
            return redirect()->route('actors.index')->with('error', $response->json('message') ?? 'Nem sikerült létrehozni.');
        }

        return redirect()->route('actors.index')->with('success', 'Színész sikeresen létrehozva.');
    }

    public function edit($id)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('actors.index')->with('error', 'Az adatmódosításhoz be kell jelentkezni.');
        }

        $response = Http::api()->get('actors');
        if ($response->failed()) {
            return redirect()->route('actors.index')->with('error', 'Nem sikerült lekérdezni a színészeket.');
        }

        $actors = $response->json('actors') ?? [];
        $actor = collect($actors)->firstWhere('id', (int)$id);

        if (!$actor) {
            return redirect()->route('actors.index')->with('error', 'A megadott színész nem található.');
        }

        return view('actors.edit', ['actor' => $actor]);
    }

    public function update(Request $request, $id)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('actors.index')->with('error', 'Az adatmódosításhoz be kell jelentkezni.');
        }

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'birth_date'  => ['nullable', 'date'],
            'gender'      => ['nullable', 'in:férfi,nő'],
            'image'       => ['nullable', 'string', 'max:255'],
        ]);

        $response = Http::api()->withToken($this->token)->patch("actors/{$id}", $validated);

        if ($response->failed()) {
            return redirect()->route('actors.index')->with('error', $response->json('message') ?? 'Nem sikerült frissíteni.');
        }

        return redirect()->route('actors.index')->with('success', 'Színész sikeresen frissítve.');
    }

    public function destroy($id)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('actors.index')->with('error', 'Az adatmódosításhoz be kell jelentkezni.');
        }

        $response = Http::api()->withToken($this->token)->delete("actors/{$id}");

        if ($response->failed()) {
            return redirect()->route('actors.index')->with('error', $response->json('message') ?? 'Nem sikerült törölni.');
        }

        return redirect()->route('actors.index')->with('success', 'Színész sikeresen törölve.');
    }

    public function exportCsv(): StreamedResponse
    {
        $response = Http::api()->get('actors');
        if ($response->failed()) {
            return redirect()->route('actors.index')->with('error', 'Nem sikerült exportálni.');
        }

        // ✅ wrapperből a listát!
        $actors = $response->json('actors') ?? [];

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="actors.csv"',
        ];

        $callback = function () use ($actors) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['ID', 'Name', 'Birth date', 'Gender', 'Description']);

            foreach ($actors as $a) {
                fputcsv($handle, [
                    $a['id'] ?? '',
                    $a['name'] ?? '',
                    $a['birth_date'] ?? '',
                    $a['gender'] ?? '',
                    $a['description'] ?? '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $response = Http::api()->get('actors');
        if ($response->failed()) {
            return redirect()->route('actors.index')->with('error', 'Nem sikerült exportálni PDF-be.');
        }

        // ✅ wrapperből a listát!
        $actors = $response->json('actors') ?? [];

        $pdf = Pdf::loadView('exports.actors_pdf', [
            'actors' => $actors,
        ]);

        return $pdf->download('actors.pdf');
    }
}
