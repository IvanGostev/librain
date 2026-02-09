@extends('layouts.app')

@section('title', 'Страница не найдена - Librain')
@section('robots', 'noindex')

@section('content')
    <div class="container d-flex flex-column align-items-center justify-content-center py-5 min-vh-75 animate-fade-in-up">
        <div class="text-center mb-5">
            <h1 class="display-1 fw-bold text-white mb-0"
                style="font-size: 8rem; text-shadow: 0 0 50px rgba(14, 165, 233, 0.3);">404</h1>
            <h2 class="display-5 fw-bold text-white mb-4">Страница не найдена</h2>
            <p class="lead text-muted mb-5 mx-auto" style="max-width: 600px;">
                К сожалению, страница, которую вы ищете, не существует. Возможно, она была удалена, или вы ошиблись в
                адресе.
            </p>

            <!-- Search Bar -->
            <form action="{{ route('search') }}" method="GET" class="w-100 mx-auto mb-5" style="max-width: 600px;">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-dark-card border-white-10 rounded-start-pill ps-4 text-secondary">
                        <i class="bi bi-search fs-4"></i>
                    </span>
                    <input type="search" name="q"
                        class="form-control bg-dark-card border-white-10 rounded-end-pill py-3 text-white placeholder-muted focus-ring-primary"
                        placeholder="Поиск книги, автора или жанра..." aria-label="Search">
                </div>
            </form>

            <a href="{{ url('/') }}" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-glow hover-elevate">
                <i class="bi bi-house-door-fill me-2"></i> Вернуться на главную
            </a>
        </div>
    </div>

    <style>
        .min-vh-75 {
            min-height: 60vh;
        }

        .bg-dark-card {
            background-color: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(10px);
        }

        .shadow-glow {
            box-shadow: 0 0 25px rgba(14, 165, 233, 0.3);
            transition: box-shadow 0.3s ease;
        }

        .shadow-glow:hover {
            box-shadow: 0 0 35px rgba(14, 165, 233, 0.5);
        }

        .hover-elevate {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-elevate:hover {
            transform: translateY(-3px);
        }
    </style>
@endsection