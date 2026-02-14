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
                                    <input class="form-check-input border-white-10" type="checkbox" name="remember"
                                        id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label text-muted small" for="remember">
                                        {{ __('Запомнить меня') }}
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid mb-4">
                                <button type="submit"
                                    class="btn btn-primary rounded-pill py-2 fw-bold shadow-lg hover-elevate text-white"
                                    style="color: #ffffff !important;">
                                    {{ __('Войти') }}
                                </button>
                            </div>

                            <div class="text-center text-white-50 small mb-3">Или через соцсети</div>

                            <div class="d-grid gap-2 mb-4">
                                <a href="{{ route('social.redirect', 'vkontakte') }}"
                                    class="btn btn-primary d-flex align-items-center justify-content-center gap-2 rounded-pill py-2 fw-semibold shadow-sm hover-elevate text-white"
                                    style="background-color: #0077FF; border-color: #0077FF; color: #ffffff !important;">
                                    <i class="bi bi-vk text-white" style="color: #ffffff !important;"></i> VK ID
                                </a>
                                <a href="{{ route('social.redirect', 'odnoklassniki') }}"
                                    class="btn btn-warning d-flex align-items-center justify-content-center gap-2 rounded-pill py-2 fw-semibold text-white shadow-sm hover-elevate"
                                    style="background-color: #EE8208; border-color: #EE8208; color: #ffffff !important;">
                                    <i class="bi bi-person text-white" style="color: #ffffff !important;"></i> Одноклассники
                                </a>
                                <div class="position-relative overflow-hidden rounded-pill">
                                    <a href="{{ route('social.redirect', 'telegram') }}"
                                        class="btn btn-info d-flex align-items-center justify-content-center gap-2 rounded-pill py-2 fw-semibold text-white shadow-sm hover-elevate w-100"
                                        style="background-color: #229ED9; border-color: #229ED9; color: #ffffff !important;">
                                        <i class="bi bi-telegram text-white" style="color: #ffffff !important;"></i>
                                        Telegram
                                    </a>
                                    <div class="position-absolute top-50 start-50 translate-middle"
                                        style="z-index: 999; opacity: 0.001; transform: scale(5);">
                                        <script async src="https://telegram.org/js/telegram-widget.js?22"
                                            data-telegram-login="{{ config('services.telegram.bot') }}" data-size="large"
                                            data-radius="20" data-auth-url="{{ route('social.callback', 'telegram') }}"
                                            data-request-access="write"></script>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center text-muted small">
                                Нет аккаунта? <a href="{{ route('register') }}"
                                    class="text-white text-decoration-none fw-semibold hover-text-primary transition-colors">Зарегистрироваться</a>
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
    </div>
    <style>
        [data-bs-theme="dark"] .form-check-input {
            background-color: #212529;
            border-color: rgba(255, 255, 255, 0.1);
        }

        [data-bs-theme="light"] .form-check-input {
            background-color: #ffffff;
            border-color: #dee2e6;
        }

        .form-check-input:checked {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
        }
    </style>
@endsection