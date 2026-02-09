<div class="mb-4 {{ $level > 0 ? 'ms-4 ms-md-5 border-start border-white-10 ps-3 ps-md-4' : '' }}">
    <div class="d-flex gap-3 mb-2">
        <div class="flex-shrink-0">
            <a href="{{ route('users.show', $review->user->id) }}" class="text-decoration-none">
                @if($review->user->avatar)
                    <img src="{{ asset('storage/' . $review->user->avatar) }}" class="rounded-circle border border-white-10"
                        style="width: 40px; height: 40px; object-fit: cover;"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="avatar bg-primary bg-opacity-10 text-primary rounded-circle d-none align-items-center justify-content-center fw-bold"
                        style="width: 40px; height: 40px; font-size: 0.9rem;">
                        {{ substr($review->user->name, 0, 1) }}
                    </div>
                @else
                    <div class="avatar bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold"
                        style="width: 40px; height: 40px; font-size: 0.9rem;">
                        {{ substr($review->user->name, 0, 1) }}
                    </div>
                @endif
            </a>
        </div>
        <div class="flex-grow-1">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <h6 class="mb-0 fw-bold">
                    <a href="{{ route('users.show', $review->user->id) }}"
                        class="text-white text-decoration-none hover-text-primary transition-colors">
                        {{ $review->user->name }}
                    </a>
                </h6>
                <span class="text-muted small">{{ $review->created_at->diffForHumans() }}</span>
            </div>
            @if($review->rating && $level == 0)
                <div class="text-warning small mb-2">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                    @endfor
                </div>
            @endif
            <p class="text-light text-opacity-75 mb-2" style="font-size: 0.95rem; line-height: 1.6;">
                {{ $review->comment }}
            </p>

            @auth
                <button class="btn btn-link btn-sm text-primary text-decoration-none p-0 fw-semibold"
                    onclick="showReplyForm({{ $review->id }})">
                    Ответить
                </button>

                <div id="reply-form-{{ $review->id }}" class="mt-3 d-none">
                    <form action="{{ route('reviews.store', $book->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $review->id }}">
                        <textarea name="comment" class="form-control form-control-dark mb-2" rows="2"
                            placeholder="Ваш ответ..."></textarea>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">Отправить</button>
                            <button type="button" class="btn btn-ghost btn-sm rounded-pill px-3"
                                onclick="hideReplyForm({{ $review->id }})">Отмена</button>
                        </div>
                    </form>
                </div>
            @endauth
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