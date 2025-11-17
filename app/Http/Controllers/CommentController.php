<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\RecommendedFilm;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function storeForFilm(Request $request, $filmId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        RecommendedFilm::findOrFail($filmId);

        Comment::create([
            'user_id' => $request->user()->id,
            'recommended_film_id' => $filmId,
            'content' => $request->content,
        ]);

// 4. Renderizzazione del partial Blade in una stringa HTML
    // Assicurati che il percorso 'recommended-films.partials.comment' sia corretto
    $html = view('recommended-films.partials.comment', ['comment' => $comment])->render();

    // 5. Risposta JSON con l'HTML incluso
    return response()->json([
        'success' => true,
        'message' => 'Commento aggiunto!',
        'html' => $html // Qui inviamo il pezzo di HTML pronto
    ]);    }

    public function destroy(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== $request->user()->id) {
            return $this->respond($request, 'Non autorizzato', back()->getTargetUrl(), 403, false);
        }

        $comment->delete();

        return $this->respond($request, 'Commento eliminato!');
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
            return redirect($redirectTo)->with($success ? 'success' : 'error', $message);
        }

        return back()->with($success ? 'success' : 'error', $message);
    }
}