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

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'recommended_film_id' => $filmId,
            'content' => $request->content,
        ]);

        $comment->load('user');
       
        $html = view('recommended-films.partials.comment', ['comment' => $comment])->render();

        return response()->json([
            'success' => true,
            'message' => 'Commento aggiunto!',
            'html' => $html 
        ]);    
    }

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