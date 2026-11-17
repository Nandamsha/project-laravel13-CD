@extends('layouts.app')
@section('title', 'Daftar Stand')
@section('content')
<div class="container page">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-6">
            <div class="kbox">
                <h2 class="mb-1">Ajukan Stand</h2>
                <p class="text-muted small">Isi data stand Anda. Pengajuan masuk daftar tunggu dan diperiksa admin.
                    Setelah diterima, pemberitahuan dikirim ke email Anda dan Anda bisa masuk.</p>
                <form method="post" action="{{ route('register') }}">@csrf
                    @php
                    $f = fn($id,$label,$type='text',$extra='') => '<label class="small text-muted mt-2"
                        for="'.$id.'">'.$label.'</label><input id="'.$id.'" name="'.$id.'" type="'.$type.'"
                        class="form-control'.($errors->has($id)?' is-invalid':'').'"
                        value="'.($type==='password'?'':e(old($id))).'" '.$extra.'>'.($errors->has($id)?'<div
                        class="text-danger small mt-1" role="alert">'.e($errors->first($id)).'</div>':'');
                    @endphp
                    {!! $f('name','Nama pemilik','text','required') !!}
                    {!! $f('stand_name','Nama stand','text','required') !!}
                    <label class="small text-muted mt-2" for="category">Kategori</label>
                    <select id="category" name="category" class="form-select">@foreach($categories as $c)<option
                            @selected(old('category')===$c)>{{ $c }}</option>@endforeach</select>
                    {!! $f('location','Lokasi di CD','text','required placeholder="Blok A-1, dekat gerbang"') !!}
                    {!! $f('whatsapp','Nomor WhatsApp pesanan','tel','required placeholder="081234567890"') !!}
                    <label class="small text-muted mt-2" for="description">Deskripsi stand</label>
                    <textarea id="description" name="description" rows="2"
                        class="form-control">{{ old('description') }}</textarea>
                    {!! $f('email','Email','email','required') !!}
                    {!! $f('password','Kata sandi (min. 8 karakter)','password','required') !!}
                    {!! $f('password_confirmation','Ulangi kata sandi','password','required') !!}
                    <button class="btn-red border-0 mt-3 w-100 justify-content-center">Kirim pengajuan</button></form>
                <p class="small mt-3 mb-0">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
