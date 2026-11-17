<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'CD Jajan') - Katalog dan Pemesanan Jajanan UMKM</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cd.css') }}">
</head>

<body>
    @php $h = request()->routeIs('home') ? '' : route('home'); @endphp
    <div id="topbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="top-contact d-flex flex-wrap"><span><i class="fas fa-clock"></i>Buka setiap
                        malam</span><span><i class="fas fa-map-marker-alt"></i>Area nongkrong CD</span></div>
                <span class="ttag"><i class="fas fa-fire me-1"></i>Pesan langsung via WhatsApp</span>
            </div>
        </div>
    </div>
    <nav class="navbar navbar-expand-lg" id="nav">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <div class="blogo">
                    <div class="bico"><i class="fas fa-utensils"></i></div>
                    <div>
                        <div class="bname">CD <span>Jajan</span></div>
                        <div class="bsub">Katalog UMKM Malam</div>
                    </div>
                </div>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navmenu"
                aria-label="Menu"><i class="fas fa-bars" style="color:var(--primary);font-size:1.35rem"></i></button>
            <div class="collapse navbar-collapse" id="navmenu">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                            href="{{ route('home') }}">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ $h }}#stand">Stand</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ $h }}#menu">Menu</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ $h }}#cara">Cara Pesan</a></li>
                </ul>
                <div class="d-flex align-items-center gap-2 nav-actions">
                    @yield('search_btn')
                    @auth
                        <a class="btn-nav-register"
                            href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('seller.dashboard') }}">Dashboard</a>
                        <form method="post" action="{{ route('logout') }}" class="m-0">@csrf<button type="submit"
                                class="btn-nav-login">Keluar</button></form>
                    @else
                        <a class="btn-nav-login" href="{{ route('login') }}">Masuk</a>
                        <a class="btn-nav-register" href="{{ route('register') }}">Daftar Stand</a>
                    @endauth
                    @yield('cart_btn')
                </div>
            </div>
        </div>
    </nav>

    @yield('content')

    <footer>
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-5">
                    <div class="fnm">CD <span>Jajan</span></div>
                    <p class="fdesc">Sistem informasi katalog dan pemesanan jajanan UMKM di CD berbasis web. Proyek
                        Kelompok 2.</p>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="ftit">Tautan</div>
                    <ul class="flinks ps-0">
                        <li><a href="{{ route('home') }}"><i class="fas fa-chevron-right"></i>Beranda</a></li>
                        <li><a href="{{ $h }}#stand"><i class="fas fa-chevron-right"></i>Stand</a></li>
                        <li><a href="{{ $h }}#menu"><i class="fas fa-chevron-right"></i>Menu</a></li>
                        <li><a href="{{ route('login') }}"><i class="fas fa-chevron-right"></i>Masuk</a></li>
                        <li><a href="{{ route('register') }}"><i class="fas fa-chevron-right"></i>Daftar Stand</a></li>
                    </ul>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="ftit">Untuk pemilik stand</div>
                    <div class="fci">
                        <div class="fciico"><i class="fas fa-store"></i></div>
                        <div class="fciinfo">Ajukan stand Anda lewat halaman Daftar Stand. Setelah diterima admin, Anda
                            bisa masuk dan mengelola menu sendiri.</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="fbot">
            <div class="container">
                <div class="d-flex justify-content-between flex-wrap gap-2">
                    <p>&copy; {{ date('Y') }} <span>CD Jajan</span> - Kelompok 2</p>
                    <p>Template: Sarab oleh Bestwpware (MIT)</p>
                </div>
            </div>
        </div>
    </footer>
    <button id="btt" aria-label="Ke atas"><i class="fas fa-chevron-up"></i></button>
    <div id="toast" role="status"></div>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        addEventListener('scroll', () => {
            document.getElementById('nav').classList.toggle('scrolled', scrollY > 60);
            document.getElementById('btt').classList.toggle('show', scrollY > 300)
        });
        document.getElementById('btt').onclick = () => scrollTo({
            top: 0,
            behavior: 'smooth'
        });
        document.querySelectorAll('#navmenu a').forEach(a => a.addEventListener('click', () => {
            const c = bootstrap.Collapse.getInstance(document.getElementById('navmenu'));
            c && c.hide()
        }));
    </script>
    @stack('scripts')
</body>

</html>
