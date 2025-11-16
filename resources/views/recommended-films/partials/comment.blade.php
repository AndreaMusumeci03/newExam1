<div class="comment" id="comment-{{ $comment->id }}">
    <div class="comment-header">
        <a href="{{ route('profile.edit') }}" class="user-name" title="Modifica Profilo" style="color: #ddd; text-decoration: none; transition: color 0.3s ease; display: flex; align-items: center; gap: 0.75rem;">
                        <img src="{{ auth()->user()->avatar_url ? Storage::url(auth()->user()->avatar_url) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=e50914&color=fff&size=32' }}" 
                             alt="Avatar" 
                             style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; margin-left:-1.8vw;">
                        {{ auth()->user()->name }}
                    </a>
        <span class="comment-date">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
    </div>
    <div class="comment-body" style="margin-left: ">
        {{ $comment->content }}
    </div>
    @if(auth()->check() && auth()->id() === $comment->user_id)
        <div class="comment-actions">
            <button 
                onclick="deleteComment({{ $comment->id }}, this)" 
                class="btn btn-danger"
                style="padding: 0.5rem 1rem; font-size: 0.9rem; margin-left: 23vw;"
            >
                🗑️ Elimina
            </button>
        </div>
    @endif
</div>