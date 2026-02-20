@php
    $authorName = $review->user ? $review->user->name : ($review->guest_name ?? 'Гость');
    $authorUrl = $review->user ? route('users.show', $review->user->id) : '#';
    $authorAvatar = $review->user ? $review->user->avatar : null;
    $authorInitial = mb_substr($authorName, 0, 1);
@endphp

<div class="mb-4 {{ $level > 0 ? 'ms-4 ms-md-5 border-start border-white-10 ps-3 ps-md-4' : '' }}">
    <div class="d-flex gap-3 mb-2">
        <div class="flex-shrink-0">
            @if($review->user)
                <a href="{{ $authorUrl }}" class="text-decoration-none">
            @else
                    <div class="text-decoration-none">
                @endif

                    @if($authorAvatar)
                        @php
                            $avatarUrl = Str::startsWith($authorAvatar, ['http://', 'https://'])
                                ? $authorAvatar
                                : asset('storage/' . $authorAvatar);
                        @endphp
                        <img src="{{ $avatarUrl }}" class="rounded-circle border border-white-10"
                            style="width: 40px; height: 40px; object-fit: cover;"
                            onerror="this.style.display='none'; this.nextElementSibling.classList.remove('d-none'); this.nextElementSibling.classList.add('d-flex');">
                        <div class="avatar bg-primary bg-opacity-10 text-primary rounded-circle d-none align-items-center justify-content-center fw-bold"
                            style="width: 40px; height: 40px; font-size: 0.9rem;">
                            {{ $authorInitial }}
                        </div>
                    @else
                        <div class="avatar bg-{{ $review->user ? 'primary' : 'secondary' }} bg-opacity-10 text-{{ $review->user ? 'primary' : 'white' }} rounded-circle d-flex align-items-center justify-content-center fw-bold"
                            style="width: 40px; height: 40px; font-size: 0.9rem;">
                            {{ $authorInitial }}
                        </div>
                    @endif

                    @if($review->user)
                        </a>
                    @else
                </div>
            @endif
    </div>
    <div class="flex-grow-1">
        <div class="d-flex align-items-center justify-content-between mb-1">
            <h6 class="mb-0 fw-bold">
                @if($review->user)
                    <a href="{{ $authorUrl }}" class="text-white text-decoration-none hover-text-primary transition-colors">
                        {{ $authorName }}
                    </a>
                @else
                    <span class="text-white">
                        {{ $authorName }}
                        <span class="badge bg-secondary bg-opacity-10 text-secondary ms-2"
                            style="font-size: 0.65rem;">Гость</span>
                    </span>
                @endif
            </h6>
            <span class="text-muted small">{{ $review->created_at->diffForHumans() }}</span>
        </div>

        <p class="text-white mb-2" style="font-size: 0.95rem; line-height: 1.6;">
            {{ $review->comment }}
        </p>

        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-link btn-sm text-primary text-decoration-none p-0 fw-semibold"
                onclick="showReplyForm({{ $review->id }})">
                Ответить
            </button>

            @php
                $likes = $review->votes->where('type', 'like')->count();
                $dislikes = $review->votes->where('type', 'dislike')->count();
                $userVote = null;
                if (auth()->check()) {
                    $userVote = $review->votes->where('user_id', auth()->id())->first();
                } else {
                    $userVote = $review->votes->where('ip_address', request()->ip())->first();
                }
                $userVoteType = $userVote ? $userVote->type : null;
            @endphp

            <div class="d-flex align-items-center gap-2">
                <button
                    class="btn btn-sm btn-link text-decoration-none p-0 {{ $userVoteType === 'like' ? 'text-success' : 'text-muted hover-text-success' }}"
                    onclick="voteReview({{ $review->id }}, 'like', this)">
                    <i class="bi bi-hand-thumbs-up{{ $userVoteType === 'like' ? '-fill' : '' }}"></i> <span
                        class="vote-count">{{ $likes }}</span>
                </button>
                <button
                    class="btn btn-sm btn-link text-decoration-none p-0 {{ $userVoteType === 'dislike' ? 'text-danger' : 'text-muted hover-text-danger' }}"
                    onclick="voteReview({{ $review->id }}, 'dislike', this)">
                    <i class="bi bi-hand-thumbs-down{{ $userVoteType === 'dislike' ? '-fill' : '' }}"></i> <span
                        class="vote-count">{{ $dislikes }}</span>
                </button>
            </div>
        </div>

        <div id="reply-form-{{ $review->id }}" class="mt-3 d-none">
            <form action="{{ route('reviews.store', $book->id) }}" method="POST">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $review->id }}">

                @guest
                    <div class="mb-2">
                        <input type="text" name="guest_name" class="form-control form-control-dark form-control-sm" required
                            placeholder="Ваше имя">
                    </div>
                @endguest

                <textarea name="comment" class="form-control form-control-dark mb-2" rows="2"
                    placeholder="Ваш ответ..."></textarea>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">Отправить</button>
                    <button type="button" class="btn btn-ghost btn-sm rounded-pill px-3"
                        onclick="hideReplyForm({{ $review->id }})">Отмена</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($review->children->count() > 0)
    <div class="mt-3">
        @foreach($review->children as $child)
            @include('catalog.books.partials.review', ['review' => $child, 'level' => $level + 1])
        @endforeach
    </div>
@endif
</div>