<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\RecommendedFilm;
use App\Models\UserFilmList;
use Illuminate\Http\Request;

class UserFilmListController extends Controller
{

    public function index()
    {
        $user = auth()->user();

        $stats = [
            'plan_to_watch' => $user->filmLists()->where('status', UserFilmList::STATUS_PLAN_TO_WATCH)->count(),
            'watching'      => $user->filmLists()->where('status', UserFilmList::STATUS_WATCHING)->count(),
            'completed'     => $user->filmLists()->where('status', UserFilmList::STATUS_COMPLETED)->count(),
            'dropped'       => $user->filmLists()->where('status', UserFilmList::STATUS_DROPPED)->count(),
        ];


        $allItems = $user->filmLists()
            ->with(['news', 'recommendedFilm'])
            ->latest()
            ->get();

        return view('my-lists.index', compact('stats', 'allItems'));
    }

  
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

        $statusLabels = UserFilmList::getStatusLabels();
        $statusLabel = $statusLabels[$status] ?? 'Lista';

        $items = $user->filmLists()
            ->with(['news', 'recommendedFilm'])
            ->where('status', $status)
            ->latest()
            ->paginate(24);

        return view('my-lists.show', compact('items', 'status', 'statusLabel'));
    }

    public function store(Request $request, $newsId)
    {
        $data = $this->validatePayload($request);

        $news = News::findOrFail($newsId);

        auth()->user()->filmLists()->updateOrCreate(
            [
                'user_id' => auth()->id(),
                'news_id' => $news->id,
            ],
            [
                'status' => $data['status'],
                'rating' => $data['rating'] ?? null,
                'personal_notes' => $data['personal_notes'] ?? null,
                'recommended_film_id' => null,
            ]
        );

        return $this->respond($request, 'Aggiunto alla lista!');
    }

    public function destroy(Request $request, $newsId)
    {
        $this->deleteBy('news_id', $newsId);
        return $this->respond($request, 'Rimosso dalla lista!');
    }

    public function storeFilm(Request $request, $filmId)
    {
        $data = $this->validatePayload($request);

        $film = RecommendedFilm::findOrFail($filmId);

        auth()->user()->filmLists()->updateOrCreate(
            [
                'user_id' => auth()->id(),
                'recommended_film_id' => $film->id,
            ],
            [
                'status' => $data['status'],
                'rating' => $data['rating'] ?? null,
                'personal_notes' => $data['personal_notes'] ?? null,
                'news_id' => null,
            ]
        );

        return $this->respond($request, 'Film aggiunto alla lista!');
    }

    public function destroyFilm(Request $request, $filmId)
    {
        $this->deleteBy('recommended_film_id', $filmId);
        return $this->respond($request, 'Film rimosso dalla lista!');
    }


    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'status' => 'required|in:plan_to_watch,watching,completed,dropped',
            'rating' => 'nullable|integer|min:1|max:10',
            'personal_notes' => 'nullable|string|max:1000',
        ]);
    }

    private function respond(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }
        return back()->with('success', $message);
    }

    private function deleteBy(string $column, $id): void
    {
        auth()->user()->filmLists()->where($column, $id)->delete();
    }
}