@extends('layouts.app')
@section('title', trim($seoTitle))
@section('description', trim($seoDescription))
@section('og_image', $author->photo ? asset('storage/' . $author->photo) : asset('favicon.svg'))
@section('schema')
    <script type="application/ld+json">
                        {
                          "@@context": "https://schema.org",
                          "@@type": "Person",
                          "name": "{{ $author->name }}",
                          "description": "{{ Str::limit(strip_tags($author->bio), 200) }}",
                          "image": "{{ $author->photo ? asset('storage/' . $author->photo) : asset('images/no-cover.svg') }}"
                        }
                        </script>
@endsection
@section('content')
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-md-4 col-lg-3 animate-fade-in-up">
                <div class="card bg-dark-card border-0 shadow-lg position-sticky" style="top: 100px;">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle overflow-hidden border border-4 border-dark shadow-xl mx-auto mb-4"
                            style="width: 150px; height: 150px;">
                            <img src="{{ $author->photo ? asset('storage/' . $author->photo) : asset('images/no-cover.svg') }}"
                                alt="{{ $author->name }}" class="w-100 h-100 object-fit-cover">
                        </div>
                        <h1 class="h3 fw-bold text-white mb-1">{{ $author->name }}</h1>
                        <div class="text-muted small mb-3">На платформе с {{ $author->created_at->format('d.m.Y') }}</div>
                        <hr class="border-white-10 my-4">
                        <div class="row text-center g-2">
                            <div class="col-6">
                                <div class="h4 fw-bold text-white mb-0">
                                    {{ number_format($author->books_count, 0, '.', ' ') }}
                                </div>
                                <div class="small text-muted">Книг</div>
                            </div>
                            <div class="col-6">
                                <div class="h4 fw-bold text-white mb-0">
                                    {{ number_format($author->views_count, 0, '.', ' ') }}
                                </div>
                                <div class="small text-muted">Просмотров</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8 col-lg-9 animate-fade-in-up delay-100">
                <div
                    class="d-flex justify-content-center justify-content-md-start gap-3 mb-4 flex-wrap animate-fade-in-up delay-100">
                    @if($filter === 'new')
                        <span class="btn btn-primary rounded-pill px-4 cursor-default">Новые</span>
                    @else
                        <a href="{{ route('authors.show', ['slug' => $author->slug, 'filter' => 'new']) }}"
                            class="btn btn-outline-light rounded-pill px-4">Новые</a>
                    @endif
                    @if($filter === 'popular')
                        <span class="btn btn-primary rounded-pill px-4 cursor-default">Популярные</span>
                    @else
                        <a href="{{ route('authors.show', ['slug' => $author->slug, 'filter' => 'popular']) }}"
                            class="btn btn-outline-light rounded-pill px-4">Популярные</a>
                    @endif
                    @if($filter === 'discussed')
                        <span class="btn btn-primary rounded-pill px-4 cursor-default">Обсуждаемое</span>
                    @else
                        <a href="{{ route('authors.show', ['slug' => $author->slug, 'filter' => 'discussed']) }}"
                            class="btn btn-outline-light rounded-pill px-4">Обсуждаемое</a>
                    @endif
                </div>
                @if($filter === 'popular')
                    <div
                        class="d-flex justify-content-center justify-content-md-start gap-2 mb-4 animate-fade-in-up delay-150 flex-wrap">
                        @foreach(['week' => 'За неделю', 'month' => 'За месяц', 'half_year' => 'За полгода', 'year' => 'За год', 'all' => 'За все время'] as $key => $label)
                            @if($period === $key)
                                <span class="btn btn-primary px-3 rounded-pill btn-sm cursor-default">{{ $label }}</span>
                            @else
                                <a href="{{ route('authors.show', ['slug' => $author->slug, 'filter' => 'popular', 'period' => $key]) }}"
                                    class="btn btn-outline-light px-3 rounded-pill btn-sm">{{ $label }}</a>
                            @endif
                        @endforeach
                    </div>
                @endif
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-2 g-sm-3">
                    @if($books->count() > 0)
                        @foreach($books as $book)
                            <div class="col animate-fade-in-up">
                                <x-book-card-vertical :book="$book" />
                            </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="text-center py-5 text-muted border border-dashed border-white-10 rounded-3">
                                <p class="mb-0">Книг не найдено.</p>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="mt-5 d-flex justify-content-center">
                    {{ $books->appends(['filter' => $filter, 'period' => $period])->links() }}
                </div>
                <div class="mt-5">
                    <h2 class="fw-bold text-white mb-4 border-start border-4 border-primary ps-3">Биография</h2>
                    <div class="bg-dark-card p-4 rounded-3 border border-white-10 mb-5 text-white-75">
                        @if($author->bio)
                            {!! nl2br(e($author->bio)) !!}
                        @else
                            <p class="text-muted fst-italic mb-0">Автор пока не добавил информацию о себе.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .btn-outline-light {
            border: 1px solid var(--bs-primary) !important;
            color: var(--bs-primary) !important;
        }
        .btn-outline-light:hover {
            background-color: var(--bs-primary) !important;
            color: white !important;
        }
        .btn-primary {
            border: 1px solid var(--bs-primary) !important;
            color: white !important;
        }
        .cursor-default {
            cursor: default !important;
        }
    </style>
@endsection
