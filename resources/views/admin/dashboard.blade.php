@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('content')
<div class="container page">
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
  <h2 class="mb-0">Dashboard Admin</h2>
  <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editAdminEmailModal">
    <i class="fas fa-envelope me-1"></i>Ubah Email ({{ auth()->user()->email }})
  </button>
</div>
@if(session('status'))<div class="alert alert-success fl-alert">{{ session('status') }}</div>@endif
@if($errors->any())<div class="alert alert-danger fl-alert"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3"><div class="kst"><b>{{ $waiting->count() }}</b>Daftar tunggu</div></div>
  <div class="col-6 col-md-3"><div class="kst"><b>{{ $stands->count() }}</b>Stand terdaftar</div></div>
  <div class="col-6 col-md-3"><div class="kst"><b>{{ $stands->where('is_active', true)->count() }}</b>Stand aktif</div></div>
  <div class="col-6 col-md-3"><div class="kst"><b>{{ $productCount }}</b>Total menu</div></div>
</div>

<h5>Daftar tunggu pengajuan ({{ $waiting->count() }})</h5>
<div class="table-responsive kt mb-4">
  <table class="table mb-0">
    <thead>
      <tr>
        <th>Stand &amp; Deskripsi</th>
        <th>Pemilik</th>
        <th>Kategori &amp; WhatsApp</th>
        <th class="text-end">Keputusan</th>
      </tr>
    </thead>
    <tbody>
    @forelse($waiting as $u)
      <tr>
        <td>
          <div class="fw-semibold text-dark">{{ $u->stand->name }}</div>
          <small class="text-muted d-block"><i class="fas fa-map-marker-alt me-1"></i>{{ $u->stand->location }}</small>
          @if($u->stand->description)<small class="text-muted d-block mt-1">{{ $u->stand->description }}</small>@endif
        </td>
        <td>
          <div>{{ $u->name }}</div>
          <small class="text-muted d-block">{{ $u->email }}</small>
          <small class="text-muted">Daftar: {{ $u->created_at->format('d/m/Y H:i') }}</small>
        </td>
        <td>
          <span class="badge bg-light text-dark border mb-1">{{ $u->stand->category }}</span><br>
          <a href="https://wa.me/{{ $u->stand->whatsapp }}" target="_blank" rel="noopener" class="small text-success">
            <i class="fab fa-whatsapp me-1"></i>{{ $u->stand->whatsapp }}
          </a>
        </td>
        <td class="text-nowrap text-end">
          <form method="post" action="{{ route('admin.approve', $u) }}" class="d-inline">@csrf<button class="sb">Terima</button></form>
          <form method="post" action="{{ route('admin.reject', $u) }}" class="d-inline" onsubmit="return confirm('Tolak pengajuan stand {{ addslashes($u->stand->name) }}?')">@csrf<button class="dbtn">Tolak</button></form>
        </td>
      </tr>
    @empty
      <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada pengajuan yang menunggu.</td></tr>
    @endforelse
    </tbody>
  </table>
</div>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
  <h5 class="mb-0">Stand terdaftar ({{ $stands->count() }})</h5>
  <input type="search" id="adminStandFilter" class="form-control form-control-sm" style="max-width:250px" placeholder="Cari nama stand, pemilik, lokasi...">
</div>
<div class="table-responsive kt">
  <table class="table mb-0" id="adminStandTable">
    <thead>
      <tr>
        <th>Stand</th>
        <th>Pemilik &amp; Kontak</th>
        <th>Kategori &amp; Menu</th>
        <th>Status</th>
        <th class="text-end">Aksi</th>
      </tr>
    </thead>
    <tbody>
    @foreach($stands as $s)
      <tr data-search="{{ strtolower($s->name.' '.$s->location.' '.$s->user->name.' '.$s->user->email.' '.$s->category) }}">
        <td>
          <div class="d-flex align-items-center gap-2">
            <img src="{{ $s->imageUrl() }}" alt="{{ $s->name }}" class="rounded border flex-shrink-0" style="width:44px;height:44px;object-fit:cover">
            <div>
              <div class="fw-semibold text-dark">{{ $s->name }}</div>
              <small class="text-muted">{{ $s->location }}</small>
            </div>
          </div>
        </td>
        <td>
          <small class="d-block text-dark">{{ $s->user->name }} ({{ $s->user->email }})</small>
          <a href="https://wa.me/{{ $s->whatsapp }}" target="_blank" rel="noopener" class="small text-success">
            <i class="fab fa-whatsapp me-1"></i>{{ $s->whatsapp }}
          </a>
        </td>
        <td>
          <div>{{ $s->category }}</div>
          <small class="text-muted">{{ $s->products_count }} menu</small>
        </td>
        <td>
          @if($s->user->status === 'rejected')
            <span class="sb off">Ditolak</span>
          @else
            <form method="post" action="{{ route('admin.stands.toggle', $s) }}">@csrf
              <button class="sb {{ $s->is_active ? '' : 'off' }}">{{ $s->is_active ? 'Aktif' : 'Nonaktif (Aktifkan)' }}</button>
            </form>
          @endif
        </td>
        <td class="text-end">
          <form method="post" action="{{ route('admin.stands.destroy', $s) }}" onsubmit="return confirm('Hapus stand {{ addslashes($s->name) }} beserta seluruh menunya?')">@csrf @method('DELETE')<button class="dbtn">Hapus</button></form>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
</div>
</div>

<div class="modal fade" id="editAdminEmailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow">
      <form method="post" action="{{ route('account.email.update') }}">
        @csrf @method('PUT')
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title">Ubah Email Admin</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <label class="small text-muted" for="admin_email">Email baru</label>
          <input id="admin_email" name="email" type="email" class="form-control mb-2" value="{{ old('email', auth()->user()->email) }}" required>
          <label class="small text-muted" for="admin_current_password">Kata sandi saat ini (konfirmasi)</label>
          <input id="admin_current_password" name="current_password" type="password" class="form-control" placeholder="Masukkan kata sandi Anda" required>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn-red border-0">Simpan Email Baru</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const adminFilter = document.getElementById('adminStandFilter');
if (adminFilter) {
  adminFilter.addEventListener('input', function () {
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('#adminStandTable tbody tr[data-search]').forEach(tr => {
      tr.style.display = tr.dataset.search.includes(q) ? '' : 'none';
    });
  });
}
</script>
@endpush
