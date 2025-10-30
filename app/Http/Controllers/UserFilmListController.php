<?php

namespace App\Http\Controllers;

use App\Models\UserFilmList;
use Illuminate\Http\Request;

class UserFilmListController extends Controller
{
    // Mostra tutte le liste dell'utente
    public function index()
    {
        $user = auth()->user();

        // ✅ Calcola le statistiche per ogni stato
        $stats = [
            'plan_to_watch' => $user->filmLists()->where('status', UserFilmList::STATUS_PLAN_TO_WATCH)->count(),
            'watching' => $user->filmLists()->where('status', UserFilmList::STATUS_WATCHING)->count(),
            'completed' => $user->filmLists()->where('status', UserFilmList::STATUS_COMPLETED)->count(),
            'dropped' => $user->filmLists()->where('status', UserFilmList::STATUS_DROPPED)->count(),
        ];

        // ✅ Ottieni tutti gli elementi (con eager loading per ottimizzare)
        $allItems = $user->filmLists()
            ->with(['news', 'recommendedFilm'])
            ->latest()
            ->get();

        return view('my-lists.index', compact('stats', 'allItems'));
    }

    // Mostra una lista specifica (es: "Da Vedere", "Completati", ecc.)
    public function show($status)
    {
        // Valida lo status
        $validStatuses = [
            UserFilmList::STATUS_PLAN_TO_WATCH,
            UserFilmList::STATUS_WATCHING,
            UserFilmList::STATUS_COMPLETED,
            UserFilmList::STATUS_DROPPED,
        ];

        if (!in_array($status, $validStatuses)) {
            abort(404, 'Stato non valido');
        }

        $user = auth()->user();

        // Ottieni il label dello status
        $statusLabels = UserFilmList::getStatusLabels();
        $statusLabel = $statusLabels[$status] ?? 'Lista';

        // Ottieni gli elementi filtrati per status
        $items = $user->filmLists()
            ->with(['news', 'recommendedFilm'])
            ->where('status', $status)
            ->latest()
            ->paginate(24);

        return view('my-lists.show', compact('items', 'status', 'statusLabel'));
    }

    // Aggiungi una news (TMDb) alla lista
    public function store(Request $request, $newsId)
    {
        $request->validate([
            'status' => 'required|in:plan_to_watch,watching,completed,dropped',
            'rating' => 'nullable|integer|min:1|max:10',
            'personal_notes' => 'nullable|string|max:1000',
        ]);

        $news = \App\Models\News::findOrFail($newsId);

        UserFilmList::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'news_id' => $newsId,
            ],
            [
                'status' => $request->status,
                'rating' => $request->rating,
                'personal_notes' => $request->personal_notes,
                'recommended_film_id' => null,
            ]
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Aggiunto alla lista!'
            ]);
        }

        return back()->with('success', 'Aggiunto alla lista!');
    }

    // Rimuovi una news dalla lista
    public function destroy(Request $request, $newsId)
    {
        auth()->user()->filmLists()->where('news_id', $newsId)->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Rimosso dalla lista!'
            ]);
        }

        return back()->with('success', 'Rimosso dalla lista!');
    }

    // ✅ Aggiungi film consigliato (OMDb) alla lista
    public function storeFilm(Request $request, $filmId)
    {
        $request->validate([
            'status' => 'required|in:plan_to_watch,watching,completed,dropped',
            'rating' => 'nullable|integer|min:1|max:10',
            'personal_notes' => 'nullable|string|max:1000',
        ]);

        $film = \App\Models\RecommendedFilm::findOrFail($filmId);

        UserFilmList::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'recommended_film_id' => $filmId,
            ],
            [
                'status' => $request->status,
                'rating' => $request->rating,
                'personal_notes' => $request->personal_notes,
                'news_id' => null,
            ]
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Film aggiunto alla lista!'
            ]);
        }

        return back()->with('success', 'Film aggiunto alla lista!');
    }

    // ✅ Rimuovi film consigliato dalla lista
    public function destroyFilm(Request $request, $filmId)
    {
        auth()->user()->filmLists()->where('recommended_film_id', $filmId)->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Film rimosso dalla lista!'
            ]);
        }

        return back()->with('success', 'Film rimosso dalla lista!');
    }
}