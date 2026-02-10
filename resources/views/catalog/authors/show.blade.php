@extends('layouts.app')

@section('title', 'Об авторе: ' . $author->name . ' | ' . config('app.name'))
@section('description', Str::limit(strip_tags($author->bio ?? 'Читайте лучшие книги автора ' . $author->name . ' в нашей библиотеке.'), 160))
@section('keywords', $author->name . ', автор, книги, биография')

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
            <!-- Sidebar Info -->
            <div class="col-md-4 col-lg-3 animate-fade-in-up">
                <div class="card bg-dark-card border-0 shadow-lg position-sticky" style="top: 100px;">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle overflow-hidden border border-4 border-dark shadow-xl mx-auto mb-4"
                            style="width: 150px; height: 150px;">
                            <img src="{{ $author->photo ? asset('storage/' . $author->photo) : asset('images/no-cover.svg') }}"
                                alt="{{ $author->name }}" class="w-100 h-100 object-fit-cover">
                        </div>
                        <h3 class="fw-bold text-white mb-1">{{ $author->name }}</h3>
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

            <!-- Main Content -->
            <div class="col-md-8 col-lg-9 animate-fade-in-up delay-100">
                <h2 class="fw-bold text-white mb-4 border-start border-4 border-primary ps-3">Биография</h2>
                <div class="bg-dark-card p-4 rounded-3 border border-white-10 mb-5 text-light text-opacity-75">
                    @if($author->bio)
                        {!! nl2br(e($author->bio)) !!}
                    @else
                        <p class="text-muted fst-italic mb-0">Автор пока не добавил информацию о себе.</p>
                    @endif
                </div>

                <h2 class="fw-bold text-white mb-4 border-start border-4 border-secondary ps-3">Книги автора</h2>

                @if($author->books->count() > 0)
                    <div class="row row-cols-1 g-3">
                        @foreach($author->books as $book)
                            <div class="col">
                                <x-book-card :book="$book" />
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 text-muted border border-dashed border-white-10 rounded-3">
                        <p class="mb-0">У автора пока нет опубликованных книг.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection