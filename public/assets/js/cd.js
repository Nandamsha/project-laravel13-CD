/* Katalog publik CD Jajan: filter, pencarian, keranjang, dan pesan WhatsApp.
   Data stand dan menu berasal dari server (window.CD), keranjang tersimpan di browser. */
(() => {
    const D = window.CD,
        A = D.assets,
        CATS = ['Semua', ...D.categories];
    const CI = {
        'Semua': 'menu/1.jpg',
        'Makanan Berat': 'menu/4.jpg',
        'Camilan': 'off-img.jpg',
        'Minuman': 'menu/3.jpg',
        'Dessert': 'menu/5.jpg',
        'Lainnya': 'menu/2.jpg'
    };
    const stands = D.stands;
    const prods = stands.flatMap(s => s.products.map(p => ({
        ...p,
        sid: s.id
    })));
    const S = {
        cat: 'Semua',
        q: '',
        sid: null,
        cart: {},
        name: '',
        note: '',
        pid: null,
        qty: 1
    };
    try {
        S.cart = JSON.parse(localStorage.getItem('cdcart') || '{}');
    } catch (e) {}
    try {
        const meta = JSON.parse(localStorage.getItem('cdcart_meta') || '{}');
        S.name = meta.name || '';
        S.note = meta.note || '';
    } catch (e) {}
    const save = () => {
        try {
            localStorage.setItem('cdcart', JSON.stringify(S.cart));
            localStorage.setItem('cdcart_meta', JSON.stringify({
                name: S.name,
                note: S.note
            }));
        } catch (e) {}
    };
    const $ = s => document.querySelector(s),
        $$ = s => document.querySelectorAll(s);
    const rp = n => 'Rp' + Number(n || 0).toLocaleString('id-ID');
    const esc = t => String(t).replace(/[&<>"]/g, c => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;'
    } [c]));
    const sd = id => stands.find(s => s.id == id),
        pd = id => prods.find(p => p.id == id);

    // Bersihkan item di localStorage yang sudah dihapus atau habis
    function cleanCart() {
        let changed = false;
        for (const id of Object.keys(S.cart)) {
            const p = pd(id);
            if (!p || !p.ok || S.cart[id] <= 0) {
                delete S.cart[id];
                changed = true;
            }
        }
        if (changed) save();
    }
    cleanCart();

    const count = () => Object.entries(S.cart).reduce((a, [id, b]) => {
        const p = pd(id);
        return (p && p.ok) ? a + b : a;
    }, 0);

    let tt;

    function toast(m) {
        const t = $('#toast');
        t.textContent = m;
        t.style.display = 'block';
        clearTimeout(tt);
        tt = setTimeout(() => t.style.display = 'none', 2200)
    }

    function go(id) {
        const t = $(id);
        if (t) scrollTo({
            top: t.offsetTop - 78,
            behavior: 'smooth'
        })
    }

    function groups() {
        const g = {};
        for (const [id, q] of Object.entries(S.cart)) {
            const p = pd(id);
            if (p && p.ok)(g[p.sid] = g[p.sid] || []).push({
                p,
                q
            })
        }
        return g
    }

    function msg(sid, it) {
        let t = 0;
        const l = it.map((x, i) => {
            t += x.p.price * x.q;
            return `${i+1}. ${x.p.name} x${x.q} = ${rp(x.p.price*x.q)}`
        });
        return `Halo ${sd(sid).name}, saya ingin memesan:\n${l.join('\n')}\n\nTotal: ${rp(t)}\nAtas nama: ${S.name||'-'}\nCatatan: ${S.note||'-'}\n\nTerima kasih!`
    }
    const cnt = c => stands.filter(s => c == 'Semua' || s.cat == c).length;

    function renderTop() {
        $('#stS').textContent = stands.length;
        $('#stP').textContent = prods.filter(p => p.ok).length;
        $('#cartCount').textContent = count();
        $('#mq').innerHTML = [...stands, ...stands].map(s => `<div class="mqitem"><i class="fas fa-circle"></i>${esc(s.name)}</div>`).join('');
        $('#sovcats').innerHTML = CATS.map(c => `<div class="sovcat ${c==S.cat?'active':''}" data-a="cat" data-id="${c}" data-go="1"><img src="${A+CI[c]}" alt="">${c}</div>`).join('');
        $('#cats').innerHTML = CATS.map(c => `<div class="col-6 col-sm-4 col-md-3 col-lg-2"><div class="catcard ${c==S.cat?'active':''}" data-a="cat" data-id="${c}" data-go="1"><img class="catimg" src="${A+CI[c]}" alt=""><div class="catnm">${c=='Semua'?'Semua Stand':c}</div><div class="catct">${cnt(c)} stand</div></div></div>`).join('');
        $$('.fl').forEach(e => e.innerHTML = CATS.map(c => `<button class="filtbtn ${c==S.cat?'active':''}" data-a="cat" data-id="${c}">${c}</button>`).join(''));
    }

    function renderStands() {
        const q = S.q.toLowerCase();
        const r = stands.filter(s => (S.cat == 'Semua' || s.cat == S.cat) && (!q || (s.name + s.desc).toLowerCase().includes(q) || prods.some(p => p.sid == s.id && p.name.toLowerCase().includes(q))));
        $('#sgrid').innerHTML = r.map(s => {
            const n = s.products.filter(p => p.ok).length;
            return `<div class="col-sm-6 col-lg-4"><div class="mcard" data-a="stand" data-id="${s.id}"><div class="mimg"><img src="${s.img}" alt=""><div class="mbdg ${n?'hot':''}">${n?n+' menu tersedia':'Menu habis'}</div></div><div class="mbody"><div class="mcat">${s.cat}</div><div class="mtit">${esc(s.name)}</div><div class="mdesc">${esc(s.desc)}</div><div class="mfoot"><div class="mdesc mb-0"><i class="fas fa-map-marker-alt me-1" style="color:var(--primary)"></i>${esc(s.loc)}</div><button class="madd" title="Lihat menu"><i class="fas fa-arrow-right"></i></button></div></div></div></div>`
        }).join('') || '<div class="empty">Stand tidak ditemukan. Coba kata kunci lain atau pilih kategori Semua.</div>'
    }

    function renderMenu() {
        const q = S.q.toLowerCase();
        const r = prods.filter(p => {
            const s = sd(p.sid);
            return (!S.sid || s.id == S.sid) && (S.cat == 'Semua' || s.cat == S.cat) && (!q || (p.name + p.desc + s.name).toLowerCase().includes(q))
        });
        $('#sfil').innerHTML = S.sid ? `<button class="sfchip" data-a="clr">Stand: ${esc(sd(S.sid).name)} <i class="fas fa-times"></i></button>` : '';
        $('#mgrid').innerHTML = r.map(p => {
            const s = sd(p.sid);
            return `<div class="col-sm-6 col-lg-4"><div class="mcard" data-a="prod" data-id="${p.id}" style="${p.ok?'':'opacity:.6'}"><div class="mimg"><img src="${p.img}" alt=""><div class="mbdg ${p.ok?'new':''}">${p.ok?'Tersedia':'Habis'}</div></div><div class="mbody"><div class="mcat">${esc(s.name)}</div><div class="mtit">${esc(p.name)}</div><div class="mdesc">${esc(p.desc)}</div><div class="mfoot"><div class="mprice">${rp(p.price)}</div><button class="madd" data-a="quick" data-id="${p.id}" title="Tambah ke keranjang" ${p.ok?'':'disabled'}><i class="fas fa-plus"></i></button></div></div></div></div>`
        }).join('') || '<div class="empty">Menu tidak ditemukan. Coba kata kunci lain.</div>'
    }

    function renderCart() {
        const g = groups(),
            ids = Object.keys(g);
        $('#cartBody').innerHTML = !ids.length ? '<div class="empty">Keranjang masih kosong.<br>Pilih stand dan tambahkan menu favoritmu.</div>' :
            `<div class="d-flex justify-content-between align-items-center mb-2"><span class="small text-muted">Data pemesan</span><button class="dbtn py-1 px-2" data-a="clear-cart" style="font-size:.75rem">Kosongkan</button></div>
<label class="small text-muted">Nama pemesan</label><input id="cn" class="form-control mb-2" value="${esc(S.name)}" placeholder="Nama kamu"><label class="small text-muted">Catatan (misal: tidak pedas, meja 5)</label><input id="cnote" class="form-control" value="${esc(S.note)}">
<p class="small text-muted mt-3 mb-0">Pesanan dikirim terpisah ke tiap penjual karena setiap stand punya nomor WhatsApp sendiri.</p>` +
            ids.map(sid => {
                const it = g[sid],
                    t = it.reduce((a, x) => a + x.p.price * x.q, 0);
                return `<div class="cgrp"><h6>${esc(sd(sid).name)}</h6>${it.map(x=>`<div class="crow"><span>${esc(x.p.name)}<br><small class="text-muted">${rp(x.p.price)}</small></span><span class="qb"><button data-a="dec" data-id="${x.p.id}" aria-label="Kurangi">-</button>${x.q}<button data-a="inc" data-id="${x.p.id}" aria-label="Tambah">+</button></span></div>`).join('')}
<div class="crow"><b>Total</b><b>${rp(t)}</b></div><pre>${esc(msg(sid,it))}</pre><button class="wabtn" data-a="order" data-id="${sid}"><i class="fab fa-whatsapp me-1"></i>Pesan via WhatsApp</button></div>`
            }).join('')
    }

    function renderAll() {
        renderTop();
        renderStands();
        renderMenu();
        renderCart()
    }

    function openPop(id) {
        const p = pd(id),
            s = sd(p.sid);
        S.pid = id;
        S.qty = 1;
        $('#mpImg').src = p.img;
        $('#mpCat').textContent = s.name;
        $('#mpTitle').textContent = p.name;
        $('#mpStars').textContent = s.cat;
        $('#mpDesc').textContent = p.desc;
        $('#mpPrice').textContent = rp(p.price);
        $('#mpMeta').innerHTML = `<div class="mpm"><div class="mpmv">${p.ok?'Ada':'Habis'}</div><div class="mpml">Ketersediaan</div></div><div class="mpm" style="flex:2"><div class="mpmv" style="font-size:.85rem">${esc(s.loc)}</div><div class="mpml">Lokasi</div></div>`;
        $('#mpTags').innerHTML = `<span class="mptag">${s.cat}</span>`;
        $('#mpQnum').textContent = 1;
        const b = $('#mpAddCart');
        b.disabled = !p.ok;
        b.innerHTML = p.ok ? '<i class="fas fa-shopping-cart"></i> Masukkan ke Keranjang' : 'Menu sedang habis';
        $('#menuPop').classList.add('open');
        document.body.style.overflow = 'hidden'
    }

    function closePop() {
        $('#menuPop').classList.remove('open');
        document.body.style.overflow = ''
    }

    function closeSearch() {
        $('#searchOv').classList.remove('open');
        document.body.style.overflow = ''
    }

    function add(id, n) {
        S.cart[id] = (S.cart[id] || 0) + n;
        if (S.cart[id] <= 0) delete S.cart[id];
        save();
        renderTop();
        renderMenu();
        renderCart()
    }

    document.addEventListener('click', e => {
        if (e.target === $('#searchOv')) closeSearch();
        if (e.target === $('#menuPop')) closePop();
        const el = e.target.closest('[data-a]');
        if (!el || el.disabled) return;
        const a = el.dataset.a,
            id = el.dataset.id;
        if (a == 'cat') {
            S.cat = id;
            S.sid = null;
            renderTop();
            renderStands();
            renderMenu();
            if (el.dataset.go) {
                closeSearch();
                go('#stand')
            }
        } else if (a == 'stand') {
            S.sid = +id;
            S.cat = 'Semua';
            renderTop();
            renderMenu();
            go('#menu')
        } else if (a == 'clr') {
            S.sid = null;
            renderMenu()
        } else if (a == 'prod') openPop(+id);
        else if (a == 'quick') {
            add(+id, 1);
            toast('Ditambahkan ke keranjang')
        } else if (a == 'inc') add(+id, 1);
        else if (a == 'dec') add(+id, -1);
        else if (a == 'clear-cart') {
            S.cart = {};
            save();
            renderTop();
            renderMenu();
            renderCart();
            toast('Keranjang dikosongkan')
        } else if (a == 'order') {
            const it = groups()[id];
            window.open(`https://wa.me/${sd(id).wa}?text=${encodeURIComponent(msg(id,it))}`, '_blank');
            it.forEach(x => delete S.cart[x.p.id]);
            save();
            toast('Pesan dibuat. Lanjutkan di WhatsApp.');
            renderTop();
            renderMenu();
            renderCart()
        }
    });
    document.addEventListener('input', e => {
        const t = e.target;
        if (t.id == 'mq2' || t.id == 'searchInput') {
            S.q = t.value;
            $('#mq2').value = $('#searchInput').value = t.value;
            renderStands();
            renderMenu()
        }
        if (t.id == 'cn') {
            S.name = t.value;
            save();
        }
        if (t.id == 'cnote') {
            S.note = t.value;
            save();
        }
    });
    $('#mpPlus').onclick = () => $('#mpQnum').textContent = ++S.qty;
    $('#mpMinus').onclick = () => {
        if (S.qty > 1) $('#mpQnum').textContent = --S.qty
    };
    $('#mpClose').onclick = closePop;
    $('#mpAddCart').onclick = function () {
        add(S.pid, S.qty);
        this.innerHTML = '<i class="fas fa-check"></i> Masuk keranjang';
        setTimeout(closePop, 700)
    };
    $('#navSearchBtn').onclick = () => {
        $('#searchOv').classList.add('open');
        document.body.style.overflow = 'hidden';
        setTimeout(() => $('#searchInput').focus(), 220)
    };
    $('#searchClose').onclick = closeSearch;
    $('#searchGo').onclick = () => {
        closeSearch();
        go('#menu')
    };
    $('#searchInput').addEventListener('keydown', e => {
        if (e.key == 'Enter') {
            closeSearch();
            go('#menu')
        }
    });
    document.addEventListener('keydown', e => {
        if (e.key == 'Escape') {
            closeSearch();
            closePop()
        }
    });
    addEventListener('scroll', () => {
        $$('section[id]').forEach(s => {
            const t = s.offsetTop - 110;
            if (scrollY >= t && scrollY < t + s.offsetHeight) {
                $$('.nav-link').forEach(l => l.classList.remove('active'));
                const l = $('.nav-link[href$="#' + s.id + '"]');
                if (l) l.classList.add('active')
            }
        })
    });
    const navLinks = [...$$('#navmenu .navbar-nav .nav-link')];
    const trackedIds = ['stand', 'menu', 'cara'];

    function setActiveNav(target) {
        navLinks.forEach(l => l.classList.remove('active'));
        const match = target === 'home' ?
            navLinks[0] :
            navLinks.find(l => (l.getAttribute('href') || '').endsWith('#' + target));
        if (match) match.classList.add('active');
    }

    function updateScrollSpy() {
        if (!navLinks.length) return;
        const atBottom = window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 12;
        if (atBottom) {
            setActiveNav('cara');
            return;
        }

        const triggerPos = window.scrollY + Math.min(220, window.innerHeight * 0.3);
        let current = 'home';

        for (const id of trackedIds) {
            const sec = document.getElementById(id);
            if (sec && triggerPos >= sec.offsetTop) {
                current = id;
            }
        }
        setActiveNav(current);
    }

    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            const href = link.getAttribute('href') || '';
            const hashIdx = href.indexOf('#');
            setActiveNav(hashIdx !== -1 ? href.slice(hashIdx + 1) : 'home');
        });
    });

    addEventListener('scroll', updateScrollSpy, {
        passive: true
    });
    addEventListener('resize', updateScrollSpy);
    updateScrollSpy();
    renderAll();
})();
