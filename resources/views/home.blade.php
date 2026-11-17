@extends('layouts.app')
@section('search_btn')<button id="navSearchBtn" title="Cari" aria-label="Cari"><i
        class="fas fa-search"></i></button>@endsection
@section('cart_btn')<button class="nav-link nav-cta border-0" data-bs-toggle="offcanvas" data-bs-target="#cartOc"><i
        class="fas fa-shopping-bag me-1"></i>Keranjang (<span id="cartCount">0</span>)</button>@endsection
@section('content')
<div id="searchOv"><button class="sovclose" id="searchClose" aria-label="Tutup"><i class="fas fa-times"></i></button>
    <div class="sovbox">
        <h4>Mau jajan apa malam ini?</h4>
        <div class="sovinput"><input type="text" id="searchInput"
                placeholder="Cari stand atau menu, misalnya sate, kopi..." autocomplete="off"><button id="searchGo"
                aria-label="Cari"><i class="fas fa-search"></i></button></div>
        <div class="sovcats" id="sovcats"></div>
    </div>
</div>

<section id="hero">
    <div class="hs hs1"></div>
    <div class="hs hs2"></div>
    <div class="hbgtxt">CD</div>
    <div class="container">
        <div class="row align-items-center g-5" style="min-height:88vh">
            <div class="col-lg-6">
                <div class="hbadge">
                    <div class="hbi"><i class="fas fa-star"></i></div><span>Semua stand UMKM di CD dalam satu
                        halaman</span>
                </div>
                <h1 class="htitle">Jajan Malam di <span class="hl">CD</span><br>Tanpa Bingung</h1>
                <p class="hdesc">Lihat stand, menu, harga, dan lokasinya dari ponsel. Pilih jajananmu, masukkan ke
                    keranjang, lalu kirim pesanan langsung ke WhatsApp penjual.</p>
                <div class="d-flex flex-wrap gap-3 mb-2"><a href="#stand" class="btn-red"><i
                            class="fas fa-store"></i>Lihat Stand</a><a href="#cara" class="btn-play">
                        <div class="pico"><i class="fas fa-question"></i></div><span>Cara Pesan</span>
                    </a></div>
                <div class="hstats d-flex gap-3 flex-wrap mt-4">
                    <div class="hstat"><span class="snum" id="stS">0</span><small>Stand Aktif</small></div>
                    <div class="sdiv"></div>
                    <div class="hstat"><span class="snum" id="stP">0</span><small>Menu Tersedia</small></div>
                    <div class="sdiv"></div>
                    <div class="hstat"><span class="snum">{{ count($data['categories']) }}</span><small>Kategori</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div style="position:relative;text-align:center">
                    <div class="hcircle"><img src="{{ asset('assets/img/banner-img.jpg') }}" alt="Jajanan"></div>
                    <div class="fcard fc1">
                        <div class="fcoi r"><i class="fas fa-store"></i></div>
                        <div><span class="fcnum">Banyak Stand</span><span class="fcsm">satu katalog</span></div>
                    </div>
                    <div class="fcard fc2">
                        <div class="fcoi y"><i class="fab fa-whatsapp"></i></div>
                        <div><span class="fcnum">WhatsApp</span><span class="fcsm">pesan otomatis</span></div>
                    </div>
                    <div class="fcard fc3">
                        <div class="fcoi g"><i class="fas fa-map-marker-alt"></i></div>
                        <div><span class="fcnum">Lokasi Jelas</span><span class="fcsm">per blok stand</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="mqsec">
    <div class="mqtrack" id="mq"></div>
</div>

<section id="category">
    <div class="container">
        <div class="text-center mb-5"><span class="slbl">Kategori</span>
            <h2 class="stitle">Cari Berdasarkan <span>Jenis Jajanan</span></h2>
            <div class="sline"></div>
        </div>
        <div class="row g-3 justify-content-center" id="cats"></div>
    </div>
</section>

<section id="stand">
    <div class="container">
        <div class="text-center mb-4"><span class="slbl">Daftar Stand</span>
            <h2 class="stitle">Stand UMKM di <span>CD</span></h2>
            <div class="sline"></div>
        </div>
        <div class="text-center mb-4 fl"></div>
        <div class="row g-4" id="sgrid"></div>
    </div>
</section>

<section id="menu" style="background:var(--cream)">
    <div class="container">
        <div class="text-center mb-4"><span class="slbl">Menu</span>
            <h2 class="stitle">Pilih <span>Jajananmu</span></h2>
            <div class="sline"></div>
        </div>
        <div class="mqbar"><input id="mq2" class="form-control" type="search" placeholder="Cari menu atau stand..."
                aria-label="Cari menu"></div>
        <div class="text-center mb-3 fl"></div>
        <div class="text-center mb-4" id="sfil"></div>
        <div class="row g-4" id="mgrid"></div>
    </div>
</section>

<section id="cara">
    <div class="container">
        <div class="text-center mb-5"><span class="slbl">Alur Pemesanan</span>
            <h2 class="stitle">Empat Langkah <span>Mudah</span></h2>
            <div class="sline"></div>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="fti">
                    <div class="ftico r"><i class="fas fa-search"></i></div>
                    <div>
                        <h6>1. Cari stand</h6>
                        <p>Telusuri stand berdasarkan kategori atau ketik nama jajanan.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="fti">
                    <div class="ftico y"><i class="fas fa-book-open"></i></div>
                    <div>
                        <h6>2. Lihat menu</h6>
                        <p>Cek harga, deskripsi, ketersediaan, dan lokasi stand.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="fti">
                    <div class="ftico g"><i class="fas fa-shopping-bag"></i></div>
                    <div>
                        <h6>3. Isi keranjang</h6>
                        <p>Atur jumlah, total harga dihitung otomatis.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="fti">
                    <div class="ftico r"><i class="fab fa-whatsapp"></i></div>
                    <div>
                        <h6>4. Kirim ke WhatsApp</h6>
                        <p>Pesan sudah tertulis rapi, tinggal kirim ke penjual.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="menuPop">
    <div class="mpbox"><button class="mpclose" id="mpClose" aria-label="Tutup"><i class="fas fa-times"></i></button>
        <div class="mpimg"><img id="mpImg" src="" alt=""></div>
        <div class="mpbody">
            <div id="mpCat"></div>
            <div id="mpTitle"></div>
            <div id="mpStars"></div>
            <div id="mpDesc"></div>
            <div id="mpPrice"></div>
            <div class="mpmeta" id="mpMeta"></div>
            <div class="mpqty"><button class="mpqbtn" id="mpMinus">-</button><span class="mpqnum"
                    id="mpQnum">1</span><button class="mpqbtn" id="mpPlus">+</button><span
                    style="font-size:.82rem;color:#aaa;margin-left:9px">porsi</span></div>
            <div class="mptags" id="mpTags"></div><button class="mpaddcart" id="mpAddCart"><i
                    class="fas fa-shopping-cart"></i>Masukkan ke Keranjang</button>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="cartOc" aria-label="Keranjang">
    <div class="offcanvas-header">
        <h4 class="m-0">Keranjang</h4><button class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body" id="cartBody"></div>
</div>
@endsection
@push('scripts')
<script>
    window.CD = @json($data);

</script>
<script src="{{ asset('assets/js/cd.js') }}"></script>
@endpush
