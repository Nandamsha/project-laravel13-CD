@extends('layouts.app')
@section('title', 'Reset Kata Sandi')
@section('content')
<div class="container page">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            <div class="kbox">
                <h2 class="mb-1">Reset Kata Sandi</h2>
                <p class="text-muted small">Masukkan email yang terdaftar, lalu buat kata sandi baru untuk akun Anda.</p>
                <form method="post" action="{{ route('password.update') }}">@csrf
                    <label class="small text-muted mt-2" for="email">Email terdaftar</label>
                    <input id="email" name="email" type="email"
                        class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required
                        autofocus>
                    @error('email')<div class="text-danger small mt-1" role="alert">{{ $message }}</div>@enderror
                    <label class="small text-muted mt-2" for="password">Kata sandi baru</label>
                    <input id="password" name="password" type="password"
                        class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')<div class="text-danger small mt-1" role="alert">{{ $message }}</div>@enderror
                    <label class="small text-muted mt-2" for="password_confirmation">Ulangi kata sandi baru</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control"
                        required>
                    <button class="btn-red border-0 mt-3 w-100 justify-content-center">Reset Kata Sandi</button>
                </form>
                <p class="small mt-3 mb-0 text-center">Ingat kata sandi Anda? <a href="{{ route('login') }}">Kembali ke
                        login</a></p>
            </div>
        </div>
    </div>
</div>
@endsection