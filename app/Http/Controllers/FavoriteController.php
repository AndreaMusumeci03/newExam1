<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = auth()->user()->favoriteNews()
            ->orderBy('favorites.created_at', 'desc')
            ->paginate(12);

        return view('favorites.index', compact('favorites'));
    }

    public function store(Request $request, $newsId)
    {
        News::findOrFail($newsId);

        $result = auth()->user()->favoriteNews()->syncWithoutDetaching([$newsId]);

        $added = !empty($result['attached']);
        $message = $added ? 'Aggiunto ai preferiti!' : 'Già presente nei preferiti!';

        return $this->respond($request, $message, success: $added);
    }

    public function destroy(Request $request, $newsId)
    {
        auth()->user()->favoriteNews()->detach($newsId);

        return $this->respond($request, 'Rimosso dai preferiti!');
    }

    private function respond(Request $request, string $message, ?string $redirectTo = null, int $status = 200, bool $success = true)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => $success,
                'message' => $message
            ], $status);
        }

        if ($redirectTo) {
            return redirect($redirectTo)->with($success ? 'success' : 'info', $message);
        }

        return back()->with($success ? 'success' : 'info', $message);
    }
}