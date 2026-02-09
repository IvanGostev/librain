@extends('layouts.app')

@section('title', 'Вход - Librain')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="text-center mb-4 animate-fade-in-up">
                    <h3 class="fw-bold text-white">С возвращением!</h3>
                    <p class="text-muted">Введите свои данные для входа</p>
                </div>

                <div class="card bg-dark-card border-0 shadow-lg animate-fade-in-up delay-100">
                    <div class="card-body p-4 p-md-5">
                        <form method="POST" action="{{ route('login') }}">
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

                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="password"
                                        class="form-label text-white-50 small text-uppercase fw-bold mb-0">{{ __('Пароль') }}</label>
                                    @if (Route::has('password.request'))
                                        <a class="text-primary small text-decoration-none hover-text-white transition-colors"
                                            href="{{ route('password.request') }}">
                                            {{ __('Забыли пароль?') }}
                                        </a>
                                    @endif
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text input-group-text-dark"><i class="bi bi-lock"></i></span>
                                    <input id="password" type="password"
                                        class="form-control form-control-dark ps-3 @error('password') is-invalid @enderror"
                                        name="password" required autocomplete="current-password" placeholder="••••••••">
                                </div>
                                @error('password')
                                    <span class="invalid-feedback d-block small mt-1" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <div class="form-check custom-checkbox">
                                    <input class="form-check-input bg-dark border-white-10" type="checkbox" name="remember"
                                        id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label text-muted small" for="remember">
                                        {{ __('Запомнить меня') }}
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid mb-4">
                                <button type="submit"
                                    class="btn btn-primary rounded-pill py-2 fw-bold shadow-lg hover-elevate">
                                    {{ __('Войти') }}
                                </button>
                            </div>

                            <div class="text-center text-muted small">
                                Нет аккаунта? <a href="{{ route('register') }}"
                                    class="text-white text-decoration-none fw-semibold hover-text-primary transition-colors">Зарегистрироваться</a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4 text-white-50 animate-fade-in-up delay-200 small">
                    &copy; {{ date('Y') }} Librain. Библиотека нового поколения.
                </div>
            </div>
        </div>
    </div>
@endsection