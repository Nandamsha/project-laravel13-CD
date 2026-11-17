@extends('layouts.app')
@section('title', 'Dashboard Penjual')
@section('content')
<div class="container page">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h2 class="mb-1">Dashboard Penjual</h2>
            <p class="text-muted mb-0">{{ $stand->name }} &middot; <span
                    class="badge {{ $stand->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $stand->is_active ? 'Stand Aktif' : 'Nonaktif' }}</span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-light text-dark border px-3 py-2">Total Menu: {{ $stand->products->count() }}</span>
            <span class="badge bg-light text-success border px-3 py-2">Tersedia:
                {{ $stand->products->where('is_available', true)->count() }}</span>
            <span class="badge bg-light text-danger border px-3 py-2">Habis:
                {{ $stand->products->where('is_available', false)->count() }}</span>
        </div>
    </div>

    @if (session('status'))
    <div class="alert alert-success fl-alert">{{ session('status') }}</div>
    @endif
    @foreach ($notices as $n)
    <div class="alert alert-success fl-alert d-flex justify-content-between align-items-center gap-2">
        <span><b>{{ $n->data['title'] ?? 'Pemberitahuan' }}.</b> {{ $n->data['message'] ?? '' }}</span>
        <form method="post" action="{{ route('seller.notices.read') }}">@csrf<button class="dbtn">Tutup</button>
        </form>
    </div>
    @endforeach
    @unless ($stand->is_active)
    <div class="alert alert-warning fl-alert">Stand Anda sedang dinonaktifkan admin dan tidak tampil di katalog publik.
    </div>
    @endunless
    @if ($errors->any())
    <div class="alert alert-danger fl-alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="kbox h-auto mb-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ $stand->imageUrl() }}" alt="{{ $stand->name }}" class="rounded-3 border"
                        style="width:64px;height:64px;object-fit:cover">
                    <div>
                        <h5 class="mb-0">Info Stand</h5>
                        <small class="text-muted">Tampil di katalog pembeli</small>
                    </div>
                </div>
                <form method="post" action="{{ route('seller.stand.update') }}" enctype="multipart/form-data">@csrf
                    @method('PUT')
                    <label class="small text-muted mt-1" for="name">Nama stand</label>
                    <input id="name" name="name" class="form-control" value="{{ old('name', $stand->name) }}" required>
                    <label class="small text-muted mt-2" for="category">Kategori</label>
                    <select id="category" name="category" class="form-select">
                        @foreach ($categories as $c)
                        <option @selected(old('category', $stand->category) === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                    <label class="small text-muted mt-2" for="location">Lokasi</label>
                    <input id="location" name="location" class="form-control"
                        value="{{ old('location', $stand->location) }}" required>
                    <label class="small text-muted mt-2" for="whatsapp">WhatsApp pesanan</label>
                    <input id="whatsapp" name="whatsapp" class="form-control"
                        value="{{ old('whatsapp', $stand->whatsapp) }}" required>
                    <label class="small text-muted mt-2" for="description">Deskripsi</label>
                    <textarea id="description" name="description" rows="2"
                        class="form-control">{{ old('description', $stand->description) }}</textarea>
                    <label class="small text-muted mt-2" for="photo">Ganti foto stand (opsional, maks. 2 MB)</label>
                    <input id="photo" name="photo" type="file" accept="image/*" class="form-control">
                    <button class="btn-red border-0 mt-3 w-100 justify-content-center">Simpan info stand</button>
                </form>
            </div>

            <div class="kbox h-auto">
                <div class="mb-2">
                    <h5 class="mb-0">Akun &amp; Email</h5>
                    <small class="text-muted">Pemilik: {{ auth()->user()->name }}</small>
                </div>
                <form method="post" action="{{ route('account.email.update') }}">
                    @csrf @method('PUT')
                    <label class="small text-muted mt-1" for="account_email">Email masuk</label>
                    <input id="account_email" name="email" type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', auth()->user()->email) }}" required>
                    <label class="small text-muted mt-2" for="current_password">Kata sandi saat ini (konfirmasi)</label>
                    <input id="current_password" name="current_password" type="password"
                        class="form-control @error('current_password') is-invalid @enderror"
                        placeholder="Masukkan kata sandi Anda" required>
                    <button class="btn-red border-0 mt-3 w-100 justify-content-center">Simpan email baru</button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="kbox h-auto mb-4">
                <h5 class="mb-3">Tambah Menu Baru</h5>
                <form method="post" action="{{ route('seller.products.store') }}" enctype="multipart/form-data">@csrf
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="small text-muted" for="new_name">Nama menu</label>
                            <input id="new_name" name="name" class="form-control" placeholder="Contoh: Es Kopi Susu"
                                required>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted" for="new_price">Harga (Rp)</label>
                            <input id="new_price" name="price" type="number" min="0" step="500" class="form-control"
                                placeholder="15000" required>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted" for="new_avail">Status awal</label>
                            <select id="new_avail" name="is_available" class="form-select">
                                <option value="1" selected>Tersedia</option>
                                <option value="0">Habis</option>
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label class="small text-muted" for="new_desc">Deskripsi singkat (opsional)</label>
                            <input id="new_desc" name="description" class="form-control"
                                placeholder="Contoh: Dingin, gula aren asli">
                        </div>
                        <div class="col-md-5">
                            <label class="small text-muted" for="new_photo">Foto menu (maks. 2 MB)</label>
                            <input id="new_photo" name="photo" type="file" accept="image/*" class="form-control">
                        </div>
                    </div>
                    <button class="btn-red border-0 mt-3"><i class="fas fa-plus me-1"></i>Tambah ke Daftar
                        Menu</button>
                </form>
            </div>

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                <h5 class="mb-0">Daftar Menu Stand ({{ $stand->products->count() }})</h5>
                <input type="search" id="sellerMenuFilter" class="form-control form-control-sm" style="max-width:240px"
                    placeholder="Cari menu di stand ini...">
            </div>

            <div class="table-responsive kt">
                <table class="table mb-0" id="sellerMenuTable">
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <th>Harga (Rp)</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stand->products as $p)
                        <tr data-name="{{ strtolower($p->name . ' ' . $p->description) }}">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $p->imageUrl() }}" alt="{{ $p->name }}"
                                        class="rounded border flex-shrink-0"
                                        style="width:46px;height:46px;object-fit:cover">
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $p->name }}</div>
                                        @if ($p->description)
                                        <small class="text-muted d-block">{{ $p->description }}</small>
                                        @endif
                                    </div>
                                </div>
                                <form id="pf{{ $p->id }}" method="post"
                                    action="{{ route('seller.products.update', $p->id) }}">@csrf @method('PUT')
                                </form>
                            </td>
                            <td>
                                <input form="pf{{ $p->id }}" name="price" type="number" min="0" step="500"
                                    class="form-control form-control-sm" value="{{ $p->price }}"
                                    aria-label="Harga {{ $p->name }}">
                            </td>
                            <td>
                                <select form="pf{{ $p->id }}" name="is_available" class="form-select form-select-sm"
                                    style="min-width:110px" aria-label="Status {{ $p->name }}">
                                    <option value="1" @selected($p->is_available)>Tersedia</option>
                                    <option value="0" @selected(!$p->is_available)>Habis</option>
                                </select>
                            </td>
                            <td class="text-nowrap text-end">
                                <button form="pf{{ $p->id }}" class="sb"
                                    title="Simpan cepat harga & status">Simpan</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2"
                                    data-bs-toggle="modal" data-bs-target="#editMenuModal{{ $p->id }}"
                                    title="Edit lengkap nama, deskripsi, foto">Edit</button>
                                <form method="post" action="{{ route('seller.products.destroy', $p->id) }}"
                                    class="d-inline"
                                    onsubmit="return confirm('Hapus menu {{ addslashes($p->name) }}?')">@csrf
                                    @method('DELETE')<button class="dbtn">Hapus</button></form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Belum ada menu. Tambahkan lewat
                                form Tambah Menu Baru di atas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@foreach ($stand->products as $p)
<div class="modal fade" id="editMenuModal{{ $p->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form method="post" action="{{ route('seller.products.update', $p->id) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">Edit Menu: {{ $p->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ $p->imageUrl() }}" alt="{{ $p->name }}" class="rounded-3 border"
                            style="width:60px;height:60px;object-fit:cover">
                        <small class="text-muted">Unggah foto baru jika ingin mengganti foto menu saat ini.</small>
                    </div>
                    <label class="small text-muted">Nama menu</label>
                    <input name="name" class="form-control mb-2" value="{{ $p->name }}" required>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="small text-muted">Harga (Rp)</label>
                            <input name="price" type="number" min="0" step="500" class="form-control"
                                value="{{ $p->price }}" required>
                        </div>
                        <div class="col-6">
                            <label class="small text-muted">Status ketersediaan</label>
                            <select name="is_available" class="form-select">
                                <option value="1" @selected($p->is_available)>Tersedia</option>
                                <option value="0" @selected(!$p->is_available)>Habis</option>
                            </select>
                        </div>
                    </div>
                    <label class="small text-muted">Deskripsi singkat</label>
                    <input name="description" class="form-control mb-2" value="{{ $p->description }}">
                    <label class="small text-muted">Ganti foto (opsional, maks. 2 MB)</label>
                    <input name="photo" type="file" accept="image/*" class="form-control">
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-red border-0">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
    const filterInput = document.getElementById('sellerMenuFilter');
    if (filterInput) {
        filterInput.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('#sellerMenuTable tbody tr[data-name]').forEach(tr => {
                tr.style.display = tr.dataset.name.includes(q) ? '' : 'none';
            });
        });
    }

</script>
@endpush
