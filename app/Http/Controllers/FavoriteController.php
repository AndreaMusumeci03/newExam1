<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
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
        $news = News::findOrFail($newsId);

        if (!auth()->user()->favoriteNews()->where('news_id', $newsId)->exists()) {
            Favorite::create([
                'user_id' => auth()->id(),
                'news_id' => $newsId,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Aggiunto ai preferiti!'
                ]);
            }

            return back()->with('success', 'Aggiunto ai preferiti!');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Già presente nei preferiti!'
            ]);
        }

        return back()->with('info', 'Già presente nei preferiti!');
    }

    public function destroy(Request $request, $newsId)
    {
        auth()->user()->favoriteNews()->detach($newsId);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Rimosso dai preferiti!'
            ]);
        }

        return back()->with('success', 'Rimosso dai preferiti!');
    }
}