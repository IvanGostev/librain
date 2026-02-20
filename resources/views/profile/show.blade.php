@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row g-5">
            <!-- Left Column: User Info -->
            <div class="col-md-4 col-lg-3 animate-fade-in-up">
                <div class="card bg-dark-card border-0 shadow-lg position-sticky" style="top: 100px;">
                    <div class="card-body text-center p-4">
                        <!-- Avatar Display/Upload -->
                        <div class="position-relative d-inline-block mb-4 {{ Auth::id() === $user->id ? 'group cursor-pointer' : '' }}"
                            @if(Auth::id() === $user->id) onclick="document.getElementById('avatar-input').click()" @endif>
                            <div class="rounded-circle overflow-hidden border border-4 border-primary shadow-xl"
                                style="width: 150px; height: 150px;">
                                @php
                                    $avatarUrl = $user->avatar 
                                        ? (Str::startsWith($user->avatar, ['http://', 'https://']) ? $user->avatar : asset('storage/' . $user->avatar))
                                        : 'https://placehold.co/400x400/334155/cbd5e1?text=' . mb_substr($user->name, 0, 1);
                                @endphp
                                <img src="{{ $avatarUrl }}"
                                    alt="{{ $user->name }}"
                                    class="w-100 h-100 object-fit-cover {{ Auth::id() === $user->id ? 'group-hover:opacity-75' : '' }} transition-opacity">
                            </div>
                            @if(Auth::id() === $user->id)
                                <div class="position-absolute top-50 start-50 translate-middle text-white opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="bi bi-camera-fill fs-2"></i>
                                </div>
                                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="avatar-form">
                                    @csrf
                                    @method('PUT')
                                    <input type="file" name="avatar" id="avatar-input" class="d-none" onchange="document.getElementById('avatar-form').submit()">
                                </form>
                            @endif
                        </div>

                        <h4 class="fw-bold text-white mb-1">{{ $user->name }}</h4>
                        <p class="text-muted small mb-3">{{ '@' . ($user->username ?? Str::slug($user->name)) }}</p>

                        <div class="d-flex justify-content-center gap-2 mb-4">
                            <span class="badge bg-body-tertiary border border-white-10 text-muted">
                                {{ $user->gender == 'male' ? 'Мужской' : ($user->gender == 'female' ? 'Женский' : 'Пол не указан') }}
                            </span>
                            <span class="badge bg-body-tertiary border border-white-10 text-muted">
                                С нами с {{ $user->created_at->format('d.m.Y') }}
                            </span>
                        </div>

                        <hr class="border-white-10 my-4">

                        @if(Auth::id() === $user->id)
                            <form action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3 text-start">
                                    <label class="form-label text-white-50 small">Имя</label>
                                    <input type="text" name="name" class="form-control bg-dark border-white-10 text-white" 
                                        value="{{ old('name', $user->name) }}" required>
                                </div>
                                <div class="mb-3 text-start">
                                    <label class="form-label text-white-50 small d-block">Пол</label>
                                    <div class="d-flex gap-3 mt-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" id="male" value="male" 
                                                {{ $user->gender == 'male' ? 'checked' : '' }}>
                                            <label class="form-check-label text-white small" for="male">Муж.</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" id="female" value="female" 
                                                {{ $user->gender == 'female' ? 'checked' : '' }}>
                                            <label class="form-check-label text-white small" for="female">Жен.</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3 text-start">
                                    <label class="form-label text-white-50 small">О себе</label>
                                    <textarea name="bio" class="form-control bg-dark border-white-10 text-white" rows="3"
                                        placeholder="Расскажите о себе...">{{ old('bio', $user->bio) }}</textarea>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-sm rounded-pill py-2 fw-bold">Сохранить изменения</button>
                                </div>
                            </form>
                        @else
                            @if($user->bio)
                                <div class="text-start">
                                    <label class="form-label text-white-50 small mb-2">О себе</label>
                                    <p class="text-white small mb-0 opacity-75">{!! nl2br(e($user->bio)) !!}</p>
                                </div>
                            @else
                                <p class="text-muted small fst-italic mb-0">Пользователь еще не заполнил информацию о себе.</p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Stats/Library? -->
            <div class="col-md-8 col-lg-9 animate-fade-in-up delay-100">
                <div class="bg-dark-card p-4 rounded-3 border border-white-10 mb-4">
                    <h4 class="fw-bold text-white mb-3">Статистика</h4>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="p-3 bg-body-tertiary rounded-3 border border-white-10 text-center">
                                <div class="h2 fw-bold text-sky mb-0">{{ $stats['finished_books'] }}</div>
                                <div class="small text-muted">Прочитано книг</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-body-tertiary rounded-3 border border-white-10 text-center">
                                <div class="h2 fw-bold text-emerald mb-0">{{ $stats['reviews_count'] }}</div>
                                <div class="small text-muted">Комментариев</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-body-tertiary rounded-3 border border-white-10 text-center">
                                <div class="h2 fw-bold text-warning mb-0">{{ $stats['days_on_site'] }}</div>
                                <div class="small text-muted">Дней на сайте</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Latest Activity -->
                <h5 class="text-white mb-3 tracking-wider fw-bold text-uppercase small">Последняя активность</h5>
                <div class="activity-list animate-fade-in-up delay-200">
                    @forelse($latestReviews as $review)
                        <div class="card bg-dark-card border-white-10 p-3 mb-3 hover-card-lift">
                            <div class="d-flex gap-3">
                                <div class="flex-shrink-0">
                                    <a href="{{ route('books.show', ['genre' => $review->book->genre_slug, 'slug' => $review->book->slug]) }}">
                                        <img src="{{ $review->book->cover_image ? asset('storage/' . $review->book->cover_image) : asset('images/no-cover.svg') }}" 
                                            class="rounded border border-white-10" 
                                            style="width: 50px; height: 75px; object-fit: cover;"
                                            onerror="this.src='{{ asset('images/no-cover.svg') }}'">
                                    </a>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <div>
                                            <span class="text-white-50 small">Оставил отзыв к книге</span>
                                            <a href="{{ route('books.show', ['genre' => $review->book->genre_slug, 'slug' => $review->book->slug]) }}" class="text-primary text-decoration-none fw-bold ms-1">
                                                {{ $review->book->title }}
                                            </a>
                                        </div>
                                        <span class="text-muted small">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    
                                    @if($review->rating > 0)
                                        <div class="text-warning mb-2" style="font-size: 0.75rem;">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                            @endfor
                                        </div>
                                    @endif

                                    <div class="text-light text-opacity-75 small fst-italic border-start border-primary border-3 ps-3 py-1">
                                        "{{ Str::limit($review->comment, 150) }}"
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted bg-dark-card rounded-3 border border-white-10 border-dashed">
                            <i class="bi bi-chat-dots fs-1 mb-3 d-block opacity-20"></i>
                            <p class="mb-0">Вы еще не оставляли отзывов.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection