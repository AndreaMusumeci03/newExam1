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
            'user_id' => $request->user()->id,
            'news_id' => $newsId,
            'content' => $request->content,
        ]);

        return $this->respond($request, 'Commento aggiunto!', route('news.show', $newsId));
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