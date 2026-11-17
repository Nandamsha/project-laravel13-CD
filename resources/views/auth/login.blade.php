@extends('layouts.app')
@section('title', 'Masuk')
@section('content')
<div class="container page">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            <div class="kbox">
                <h2 class="mb-1">Masuk</h2>
                <p class="text-muted small">Khusus pemilik stand yang pengajuannya sudah diterima, dan admin. Pembeli
                    tidak perlu masuk.</p>
                                    @if (session('status'))
                <div class="alert alert-success small" role="status">{{ session('status') }}</div>
                @endif
                <form method="post" action="{{ route('login') }}">@csrf
                    <label class="small text-muted mt-2" for="email">Email</label>
                    <input id="email" name="email" type="email"
                        class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required
                        autofocus>
                    @error('email')<div class="text-danger small mt-1" role="alert">{{ $message }}</div>@enderror
                    <div class="d-flex justify-content-between align-items-center mt-2"><label class="small text-muted" for="password">Kata sandi</label><a class="small" href="{{ route('password.request') }}">Lupa kata sandi?</a></div>
                    <input id="password" name="password" type="password" class="form-control" required>
                    <div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="remember"
                            id="remember"><label class="form-check-label small" for="remember">Ingat saya</label></div>
                    <button class="btn-red border-0 mt-3 w-100 justify-content-center">Masuk</button>
                </form>
                <p class="small mt-3 mb-0">Belum punya stand di sistem? <a href="{{ route('register') }}">Ajukan stand
                        Anda</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
