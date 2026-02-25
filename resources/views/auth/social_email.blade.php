@extends('layouts.app')
@section('title', 'Укажите Email - Librain')
@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="text-center mb-4 animate-fade-in-up">
                    <h3 class="fw-bold text-white">Укажите Email</h3>
                    <p class="text-muted">Ваш провайдер не передал нам ваш email</p>
                </div>
                <div class="card bg-dark-card border-0 shadow-lg animate-fade-in-up delay-100">
                    <div class="card-body p-4 p-md-5">
                        <form method="POST" action="{{ route('social.email.store') }}">
                            @csrf
                            <div class="mb-3">
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
                                    class="btn btn-secondary rounded-pill py-2 fw-bold shadow-lg hover-elevate text-white"
                                    style="color: #ffffff !important;">
                                    {{ __('Продолжить') }}
                                </button>
                            </div>
                            <div class="text-center text-muted small">
                                Не хотите указывать? <a href="{{ route('login') }}"
                                    class="text-white text-decoration-none fw-semibold hover-text-secondary transition-colors">Отмена</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection