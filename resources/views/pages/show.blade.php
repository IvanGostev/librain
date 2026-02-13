@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card bg-dark-card border-0 shadow-lg animate-fade-in-up">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="h2 fw-bold text-white mb-4">{{ $page->title }}</h1>
                        <div class="text-white-50 page-content">
                            {!! $page->content !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .page-content h1,
        .page-content h2,
        .page-content h3 {
            color: white;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
        }

        .page-content p {
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .page-content ul,
        .page-content ol {
            margin-bottom: 1rem;
            padding-left: 1.5rem;
        }

        .page-content li {
            margin-bottom: 0.5rem;
        }
    </style>
@endsection