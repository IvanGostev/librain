@extends('layouts.app')

@section('title', 'Регистрация - Librain')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="text-center mb-4 animate-fade-in-up">
                    <h3 class="fw-bold text-white">Создать аккаунт</h3>
                    <p class="text-muted">Присоединяйтесь к сообществу читателей</p>
                </div>

                <div class="card bg-dark-card border-0 shadow-lg animate-fade-in-up delay-100">
                    <div class="card-body p-4 p-md-5">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <!-- Username -->
                            <div class="mb-3">
                                <label for="username"
                                    class="form-label text-white-50 small text-uppercase fw-bold">{{ __('Никнейм') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text input-group-text-dark"><i class="bi bi-at"></i></span>
                                    <input id="username" type="text"
                                        class="form-control form-control-dark ps-3 @error('username') is-invalid @enderror"
                                        name="username" value="{{ old('username') }}" required autocomplete="username"
                                        autofocus placeholder="username">
                                </div>
                                @error('username')
                                    <span class="invalid-feedback d-block small mt-1" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name"
                                    class="form-label text-white-50 small text-uppercase fw-bold">{{ __('Имя') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text input-group-text-dark"><i class="bi bi-person"></i></span>
                                    <input id="name" type="text"
                                        class="form-control form-control-dark ps-3 @error('name') is-invalid @enderror"
                                        name="name" value="{{ old('name') }}" required autocomplete="name"
                                        placeholder="Иван Иванов">
                                </div>
                                @error('name')
                                    <span class="invalid-feedback d-block small mt-1" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email"
                                    class="form-label text-white-50 small text-uppercase fw-bold">{{ __('Email') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text input-group-text-dark"><i
                                            class="bi bi-envelope"></i></span>
                                    <input id="email" type="email"
                                        class="form-control form-control-dark ps-3 @error('email') is-invalid @enderror"
                                        name="email" value="{{ old('email') }}" required autocomplete="email"
                                        placeholder="name@example.com">
                                </div>
                                @error('email')
                                    <span class="invalid-feedback d-block small mt-1" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password"
                                    class="form-label text-white-50 small text-uppercase fw-bold">{{ __('Пароль') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text input-group-text-dark"><i class="bi bi-lock"></i></span>
                                    <input id="password" type="password"
                                        class="form-control form-control-dark ps-3 @error('password') is-invalid @enderror"
                                        name="password" required autocomplete="new-password" placeholder="••••••••">
                                </div>
                                @error('password')
                                    <span class="invalid-feedback d-block small mt-1" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label for="password-confirm"
                                    class="form-label text-white-50 small text-uppercase fw-bold">{{ __('Подтверждение пароля') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text input-group-text-dark"><i
                                            class="bi bi-check2-circle"></i></span>
                                    <input id="password-confirm" type="password" class="form-control form-control-dark ps-3"
                                        name="password_confirmation" required autocomplete="new-password"
                                        placeholder="••••••••">
                                </div>
                            </div>

                            <div class="d-grid mb-4">
                                <button type="submit"
                                    class="btn btn-secondary rounded-pill py-2 fw-bold shadow-lg hover-elevate text-white">
                                    {{ __('Зарегистрироваться') }}
                                </button>
                            </div>

                            <div class="text-center text-muted small">
                                Уже есть аккаунт? <a href="{{ route('login') }}"
                                    class="text-white text-decoration-none fw-semibold hover-text-secondary transition-colors">Войти</a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4 text-white-50 animate-fade-in-up delay-200 small">
                    Нажимая "Зарегистрироваться", вы соглашаетесь с правилами сервиса.
                </div>
            </div>
        </div>
    </div>
@endsection