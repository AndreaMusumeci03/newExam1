<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, $newsId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        Comment::create([
            'user_id' => auth()->id(),
            'news_id' => $newsId,
            'content' => $request->content,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Commento aggiunto!'
            ]);
        }

        return redirect()->route('news.show', $newsId)->with('success', 'Commento aggiunto!');
    }

    // Elimina un commento
    public function destroy(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        // Controlla che l'utente sia il proprietario del commento
        if ($comment->user_id !== auth()->id()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorizzato'
                ], 403);
            }
            abort(403);
        }

        $comment->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Commento eliminato!'
            ]);
        }

        return back()->with('success', 'Commento eliminato!');
    }
}