@extends('layouts.app')

@section('title', 'Сброс пароля - Librain')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="text-center mb-4 animate-fade-in-up">
                    <h3 class="fw-bold text-white">Сброс пароля</h3>
                    <p class="text-muted">Введите ваш Email для получения ссылки</p>
                </div>

                <div class="card bg-dark-card border-0 shadow-lg animate-fade-in-up delay-100">
                    <div class="card-body p-4 p-md-5">
                        @if (session('status'))
                            <div class="alert alert-success bg-success bg-opacity-10 text-success border-success border-opacity-25 rounded-3 mb-4"
                                role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="mb-4">
                                <label for="email"
                                    class="form-label text-white-50 small text-uppercase fw-bold">{{ __('Email') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text input-group-text-dark"><i
                                            class="bi bi-envelope"></i></span>
                                    <input id="email" type="email"
                                        class="form-control form-control-dark ps-3 @error('email') is-invalid @enderror"
                                        name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                        placeholder="name@example.com">
                                </div>
                                @error('email')
                                    <span class="invalid-feedback d-block small mt-1" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="d-grid mb-4">
                                <button type="submit"
                                    class="btn btn-primary rounded-pill py-2 fw-bold shadow-lg hover-elevate text-white"
                                    style="color: #ffffff !important;">
                                    {{ __('Отправить ссылку для сброса') }}
                                </button>
                            </div>

                            <div class="text-center text-muted small">
                                Вспомнили пароль? <a href="{{ route('login') }}"
                                    class="text-white text-decoration-none fw-semibold hover-text-primary transition-colors">Войти</a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4 text-white-50 animate-fade-in-up delay-200 small">
                    &copy; {{ date('Y') }} Librain.
                </div>
            </div>
        </div>
    </div>
@endsection