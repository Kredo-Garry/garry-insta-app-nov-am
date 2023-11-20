{{-- We will inject/include this comment file into the body.blade.php --}}
<div class="mt-3">
    {{-- Show all the comments here --}}
    @if ($post->comments->isNotEmpty())
    <hr>
        <ul class="list-group">
            @foreach ($post->comments->take(3) as $comment)
                <li class="list-group-item border-0 p-0 mb-2">
                    <a href="{{route('profile.show', $comment->user->id)}}" class="text-decoration-none text-dark fw-bold">{{ $comment->user->name }}</a>
                    &nbsp;
                    <p class="d-inline fw-light">{{ $comment->body }}</p>

                    <form action="#" method="post">
                        @csrf
                        @method('DELETE')
                        <p><span class="text-danger small">Posted on {{ $comment->created_at->diffForHumans() }}</span></p>
                        <span class="text-uppercase text-muted small">{{date('M d, Y', strtotime($comment->created_at))}}</span>

                        {{-- If the AUTH user is the owner of the comment, then display the delete button --}}
                        @if (Auth::user()->id === $comment->user->id)
                            &middot;
                            <button type="submit" class="border bg-transparent text-danger p-0 small">Delete</button>
                        @endif
                    </form>
                </li>
            @endforeach
        </ul>
    @endif

    @if ($post->comments->count() > 3)
        <li class="list-group-item border-0 px-0 pt-0">
            <a href="{{ route('post.show', $post->id) }}" class="text-decoration-none small">View All {{ $post->comments->count() }} comments</a>
        </li>
    @endif

    <form action="{{ route('comment.store', $post->id) }}" method="post">
        @csrf
        <div class="input-group">
            <textarea name="comment_body{{$post->id}}" rows="1" class="form-control form-control-sm" placeholder="Add a comment here...">{{ old('comment_body' . $post->id) }}</textarea>
            <button type="submit" class="btn btn-secondary btn-sm">Post</button>
        </div>
        @error('comment_body' . $post->id)
            <p class="text-danger small">{{ $message }}</p>
        @enderror
    </form>
</div>
