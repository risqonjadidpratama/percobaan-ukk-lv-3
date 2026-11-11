// Mode terang / gelap: dua tombol terpisah, pilihan disimpan di browser (localStorage)
(function () {
    var KEY = 'tema';
    var t = null;
    try { t = localStorage.getItem(KEY); } catch (e) {}
    if (!t) {
        t = (window.matchMedia && matchMedia('(prefers-color-scheme: dark)').matches) ? 'gelap' : 'terang';
    }
    document.documentElement.setAttribute('data-tema', t);

    document.addEventListener('DOMContentLoaded', function () {
        var box = document.createElement('div');
        box.className = 'tema-switch';
        var daftar = [['terang', '☀️ Terang'], ['gelap', '🌙 Gelap']];
        var tombol = {};

        function tandai() {
            var aktif = document.documentElement.getAttribute('data-tema');
            for (var k in tombol) {
                tombol[k].className = (k === aktif) ? 'aktif' : '';
            }
        }
        function pilih(nama) {
            document.documentElement.setAttribute('data-tema', nama);
            try { localStorage.setItem(KEY, nama); } catch (e) {}
            tandai();
        }

        daftar.forEach(function (d) {
            var b = document.createElement('button');
            b.type = 'button';
            b.textContent = d[1];
            b.onclick = function () { pilih(d[0]); };
            tombol[d[0]] = b;
            box.appendChild(b);
        });
        tandai();
        document.body.appendChild(box);
    });
})();