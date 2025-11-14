<div class="comment" id="comment-{{ $comment->id }}">
    <div class="comment-header">
        <span class="comment-author">{{ $comment->user->name }}</span>
        <span class="comment-date">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
    </div>
    <div class="comment-body">
        {{ $comment->content }}
    </div>
    @if(auth()->check() && auth()->id() === $comment->user_id)
        <div class="comment-actions">
            <button 
                onclick="deleteComment({{ $comment->id }}, this)" 
                class="btn btn-danger"
                style="padding: 0.5rem 1rem; font-size: 0.9rem;"
            >
                🗑️ Elimina
            </button>
        </div>
    @endif
</div>