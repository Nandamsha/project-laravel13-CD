@extends('layouts.app')
@section('title', 'Pengajuan Terkirim')
@section('content')
<div class="container page">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            <div class="kbox text-center">
                <div style="font-size:2.4rem;color:var(--secondary)"><i class="fas fa-hourglass-half"></i></div>
                <h2 class="mb-2">Pengajuan Terkirim</h2>
                <p class="text-muted">Stand <b>{{ $submitted['stand'] }}</b> masuk ke <b>daftar tunggu</b>. Admin akan
                    memeriksa pengajuan Anda, dan pemberitahuan dikirim ke <b>{{ $submitted['email'] }}</b> setelah
                    diterima. Setelah itu Anda bisa masuk dan mengelola stand.</p>
                <a class="btn-red" href="{{ route('home') }}">Kembali ke beranda</a>
            </div>
        </div>
    </div>
</div>
@endsection
