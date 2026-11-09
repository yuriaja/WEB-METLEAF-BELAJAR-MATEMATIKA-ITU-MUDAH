<!doctype html>
<html lang="id">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Masuk | MathLeaf</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: #f0fdf4;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border-left: 1px solid rgba(255, 255, 255, 0.9);
            box-shadow: -20px 0 60px -15px rgba(22, 163, 74, 0.2);
        }

        .input-math {
            background: rgba(255, 255, 255, 0.8);
            border: 2px solid #bbf7d0;
            transition: all 0.2s;
        }

        .input-math:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2);
        }

        .title-glow {
            text-shadow: 0 15px 35px rgba(255, 255, 255, 0.9);
        }

        .class-code-input {
            width: min(100%, 330px);
            padding: .9rem 1rem;
            border: 2px solid #86efac;
            border-radius: 16px;
            font: 800 1.35rem ui-monospace, SFMono-Regular, Consolas, monospace;
            letter-spacing: .17em;
            text-align: center;
            color: #064e3b;
            text-transform: uppercase;
            background: rgba(255, 255, 255, .85);
            box-shadow: 0 8px 30px rgba(6, 95, 70, .08);
            outline: none;
        }

        .class-code-input:focus {
            border-color: #059669;
            box-shadow: 0 0 0 4px #a7f3d055;
        }

        .class-code-hint {
            font-size: .79rem;
            color: #047857;
            font-weight: 600;
            margin-top: .5rem;
        }
    </style>
</head>

<body class="h-screen w-full overflow-hidden flex flex-col md:flex-row relative">

    <!-- Canvas Background 3D -->
    <canvas id="mathPlantCanvas" class="absolute inset-0 w-full h-full z-0 cursor-pointer"></canvas>

    <!-- Sisi Kiri: Logo, Judul, Caption & Kotak Kode Kelas -->
    <div class="flex-1 flex flex-col justify-center px-8 md:px-16 lg:px-24 z-10 pointer-events-none pb-12 md:pb-0">
        <div class="max-w-2xl relative z-10">
            <div class="flex items-center gap-4 lg:gap-6 mb-6">
                <div
                    class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-tr from-emerald-500 to-green-400 rounded-3xl flex items-center justify-center text-white shadow-xl rotate-3 border-2 border-white">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 20A7 7 0 0 1 4 13C4 5 14 3 21 3c0 7-2 17-10 17Z" />
                        <path d="M4 21c0-6 5-11 11-14" />
                    </svg>
                </div>
                <h1 class="text-5xl md:text-6xl font-black text-emerald-950 tracking-tighter title-glow">
                    Math<span class="text-emerald-500">Leaf</span>
                </h1>
            </div>
            <h2 class="text-3xl font-bold text-emerald-900 mb-4">Senang bertemu lagi!</h2>
            <p class="text-lg text-emerald-800 font-medium max-w-md">Masuk untuk melanjutkan perjalanan belajarmu hari
                ini.</p>

            <div id="left-kode-kelas-container" class="mt-10 pointer-events-auto transition-all duration-300">
                <label for="class_code_visible" class="block text-emerald-900 font-bold text-base mb-2">Kode Kelas Matematika</label>
                <p class="text-sm text-emerald-700 font-medium mb-3">Masukkan 8 karakter kode kelas dari guru setiap kali login.</p>
                <input id="class_code_visible" type="text" class="class-code-input" maxlength="8" minlength="8"
                    inputmode="text" pattern="[A-Za-z0-9]{8}" autocomplete="off" autocapitalize="characters"
                    spellcheck="false" placeholder="CONTOH8" aria-describedby="class-code-hint" required>
                <div id="class-code-hint" class="class-code-hint">Wajib diisi setiap login siswa.
                </div>
            </div>
        </div>
    </div>

    <!-- Sisi Kanan: Panel Form Login -->
    <div
        class="w-full md:w-[440px] lg:w-[480px] h-full glass-panel z-20 flex flex-col justify-center px-8 lg:px-12 absolute md:relative bottom-0 md:bottom-auto pointer-events-auto rounded-t-[3rem] md:rounded-none md:rounded-l-[3rem] border-t md:border-t-0 border-white overflow-y-auto py-10">

        <form action="/api/mathleaf/login" method="POST" class="flex flex-col gap-5">
            @csrf

            @if (request()->query('registered') === '1')
                <div
                    class="bg-emerald-100 border border-emerald-300 text-emerald-900 text-sm font-semibold px-4 py-3 rounded-xl text-center">
                    Pendaftaran siswa berhasil! Masukkan email, kata sandi, dan kode kelas untuk bergabung ke kelas
                    pertama kali.
                </div>
            @endif
            <!-- Notifikasi Sukses Daftar -->
            @if (session('success'))
                <div
                    class="bg-emerald-100 border border-emerald-300 text-emerald-800 text-sm font-semibold px-4 py-3 rounded-xl shadow-sm text-center">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <!-- Nilai default diambil dari old('role'), jika kosong default 'siswa' -->
            <input type="hidden" name="role" id="role-input" value="{{ old('role', 'siswa') }}">
            <input type="hidden" name="kode_kelas" id="hidden_class_code" value="">
            <!-- Role Switcher -->
            <div class="flex p-1 bg-white/50 rounded-xl border border-emerald-100 mb-2">
                <button type="button"
                    class="role-btn w-1/2 py-2.5 rounded-lg text-sm font-bold transition-all text-emerald-700"
                    data-role="siswa">
                    Siswa
                </button>
                <button type="button"
                    class="role-btn w-1/2 py-2.5 rounded-lg text-sm font-bold transition-all text-emerald-700"
                    data-role="guru">
                    Guru
                </button>
            </div>

            <div>
                <label class="block text-emerald-900 font-bold text-sm mb-2">Alamat Email</label>
                <!-- Menangkap old('email') dari proses daftar tadi -->
                <input type="email" name="email" value="{{ old('email') }}"
                    class="input-math w-full rounded-xl px-4 py-3 text-emerald-900" placeholder="nama@email.com"
                    required>
            </div>

            <div>
                <label class="block text-emerald-900 font-bold text-sm mb-2">Kata Sandi</label>
                <input type="password" name="password" class="input-math w-full rounded-xl px-4 py-3 text-emerald-900"
                    placeholder="Ketik ulang kata sandimu" required>
            </div>

            <button type="submit"
                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-xl transition-all shadow-lg shadow-emerald-500/30 mt-4">
                Masuk Sekarang
            </button>

            <p class="text-center text-sm text-emerald-800 mt-4 font-medium">
                Belum punya akun? <a href="/daftar" class="text-emerald-600 font-bold hover:underline">Daftar Gratis</a>
            </p>
            <div class="text-center mt-6 text-xs text-emerald-700 opacity-60">
                <a href="/" class="hover:underline">← Kembali ke Beranda</a>
            </div>
        </form>
    </div>

    <!-- Script Logika & Animasi 3D -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const roleBtns = document.querySelectorAll('.role-btn');
            const roleInput = document.getElementById('role-input');
            const leftKodeKelasContainer = document.getElementById('left-kode-kelas-container');
            const hiddenInput = document.getElementById('hidden_class_code');
            const visibleCodeInput = document.getElementById('class_code_visible');
            const form = document.querySelector('form');
            let errorMessage;

            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton.disabled) return;
                if (roleInput.value === 'siswa') {
                    hiddenInput.value = visibleCodeInput.value.trim().toUpperCase();
                    if (!/^[A-Z0-9]{8}$/.test(hiddenInput.value)) {
                        visibleCodeInput.setCustomValidity('Masukkan kode kelas tepat 8 karakter.');
                        visibleCodeInput.reportValidity();
                        return;
                    }
                } else {
                    hiddenInput.value = '';
                }
                if (!errorMessage) {
                    errorMessage = document.createElement('div');
                    errorMessage.setAttribute('role', 'alert');
                    errorMessage.setAttribute('aria-live', 'polite');
                    errorMessage.className =
                        'bg-red-100 border border-red-300 text-red-800 text-sm font-semibold px-4 py-3 rounded-xl shadow-sm text-center';
                    form.prepend(errorMessage);
                }
                errorMessage.textContent = '';
                errorMessage.hidden = true;

                const buttonLabel = submitButton.textContent;
                submitButton.disabled = true;
                submitButton.textContent = 'Masuk...';

                try {
                    // URL relatif memastikan request memakai domain & HTTPS yang dibuka pengguna.
                    const response = await fetch(form.getAttribute('action'), {
                        method: 'POST',
                        mode: 'same-origin',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify(Object.fromEntries(new FormData(form))),
                    });

                    if (response.status === 419) {
                        throw new Error(
                            'Sesi halaman sudah kedaluwarsa. Muat ulang halaman, lalu coba lagi.');
                    }

                    const contentType = response.headers.get('content-type') || '';
                    if (!contentType.includes('application/json')) {
                        throw new Error(
                            `Server tidak mengirim respons JSON (HTTP ${response.status}). Periksa route dan server Laravel.`
                        );
                    }

                    const result = await response.json();
                    if (!response.ok) {
                        const firstError = Object.values(result.errors || {}).flat()[0];
                        throw new Error(firstError || result.message ||
                            'Gagal masuk. Silakan coba lagi.');
                    }

                    if (!result.redirect) {
                        throw new Error('Berhasil masuk, tetapi alamat halaman tujuan belum tersedia.');
                    }

                    // Jaga redirect tetap di host saat ini (termasuk bila backend memberi URL localhost/HTTP).
                    const target = new URL(String(result.redirect), window.location.origin);
                    if (!['http:', 'https:'].includes(target.protocol)) {
                        throw new Error('Alamat tujuan setelah login tidak valid.');
                    }
                    window.location.assign(target.pathname + target.search + target.hash);
                } catch (error) {
                    errorMessage.hidden = false;
                    errorMessage.textContent = error instanceof TypeError ?
                        'Gagal menghubungi server. Periksa koneksi ngrok, pastikan Laravel masih berjalan, lalu coba lagi.' :
                        (error.message || 'Terjadi kesalahan. Coba lagi.');
                } finally {
                    submitButton.disabled = false;
                    submitButton.textContent = buttonLabel;
                }
            });

            const renderRole = (role) => {
                roleInput.value = role;
                roleBtns.forEach((btn) => {
                    const active = btn.dataset.role === role;
                    btn.classList.toggle('bg-emerald-500', active);
                    btn.classList.toggle('text-white', active);
                    btn.classList.toggle('shadow-md', active);
                    btn.classList.toggle('text-emerald-700', !active);
                    btn.setAttribute('aria-pressed', active ? 'true' : 'false');
                });
                const student = role === 'siswa';
                leftKodeKelasContainer.hidden = !student;
                leftKodeKelasContainer.style.display = student ? '' : 'none';
                visibleCodeInput.required = student;
                if (!student) {
                    visibleCodeInput.value = '';
                    hiddenInput.value = '';
                }
            };
            roleBtns.forEach((btn) => btn.addEventListener('click', () => renderRole(btn.dataset.role)));
            visibleCodeInput.addEventListener('input', () => {
                visibleCodeInput.value = visibleCodeInput.value.toUpperCase().replace(/[^A-Z0-9]/g, '')
                    .slice(0, 8);
                visibleCodeInput.setCustomValidity('');
                hiddenInput.value = visibleCodeInput.value;
            });
            renderRole(roleInput.value === 'guru' ? 'guru' : 'siswa');
        });


        // 3. Script Canvas 3D Math Plant
        const canvas = document.getElementById('mathPlantCanvas');
        const ctx = canvas.getContext('2d');
        let w, h;

        function resize() {
            w = canvas.width = window.innerWidth;
            h = canvas.height = window.innerHeight;
        }
        window.addEventListener('resize', resize);
        resize();
        const leaves = [];
        const bubbles = [];
        const mathChars = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '+', '−', '×', '÷', '=', '%', '½'];
        let rootX = w * 0.15;
        let rootY = h + 100;

        function addLeaf(targetX, targetY) {
            let baseAng = (Math.random() * Math.PI) - (Math.random() * Math.PI);
            leaves.push({
                x: targetX,
                y: targetY,
                baseAngle: baseAng,
                angle: baseAng,
                scale: 0,
                maxScale: Math.random() * 0.6 + 0.8,
                growthRate: 0.05,
                swaySpeed: Math.random() * 0.0015 + 0.001,
                swayOffset: Math.random() * Math.PI * 2,
                swayAmount: Math.random() * 0.15 + 0.05
            });
        }
        for (let i = 0; i < 8; i++) setTimeout(() => {
            addLeaf(w * 0.15 + (Math.random() * (w * 0.4) - (w * 0.2)), h * 0.8 - (Math.random() * (h * 0.6)));
        }, i * 250);

        function drawLeaf(leaf) {
            ctx.save();
            ctx.translate(leaf.x, leaf.y);
            ctx.rotate(leaf.angle);
            ctx.scale(leaf.scale, leaf.scale);
            ctx.beginPath();
            ctx.moveTo(0, 0);
            ctx.quadraticCurveTo(35, -35, 70, -10);
            ctx.quadraticCurveTo(35, 25, 0, 0);
            let gradient = ctx.createLinearGradient(0, 0, 70, 0);
            gradient.addColorStop(0, '#34d399');
            gradient.addColorStop(1, '#059669');
            ctx.fillStyle = gradient;
            ctx.fill();
            ctx.strokeStyle = 'rgba(255,255,255,0.4)';
            ctx.lineWidth = 1.5;
            ctx.beginPath();
            ctx.moveTo(0, 0);
            ctx.quadraticCurveTo(35, -10, 65, -10);
            ctx.stroke();
            ctx.restore();
        }

        function spawnBubble(x, y) {
            bubbles.push({
                x: x,
                y: y,
                char: mathChars[Math.floor(Math.random() * mathChars.length)],
                speedY: -(Math.random() * 1.5 + 0.5),
                speedX: (Math.random() - 0.5) * 1.0,
                opacity: 1,
                size: Math.random() * 12 + 12,
                drift: Math.random() * Math.PI * 2
            });
        }

        function animate(time) {
            ctx.clearRect(0, 0, w, h);
            rootX = w * 0.15;
            ctx.strokeStyle = '#6ee7b7';
            ctx.lineWidth = 3;
            ctx.lineCap = 'round';
            let rootSway = Math.sin(time * 0.0005) * 15;
            leaves.forEach(leaf => {
                leaf.angle = leaf.baseAngle + Math.sin(time * leaf.swaySpeed + leaf.swayOffset) * leaf.swayAmount;
                ctx.beginPath();
                ctx.moveTo(rootX + rootSway, rootY);
                ctx.quadraticCurveTo(rootX, leaf.y + 150, leaf.x, leaf.y);
                ctx.stroke();
            });
            leaves.forEach(leaf => {
                if (leaf.scale < leaf.maxScale) leaf.scale += leaf.growthRate;
                drawLeaf(leaf);
                if (Math.random() < 0.003) spawnBubble(leaf.x, leaf.y);
            });
            for (let i = bubbles.length - 1; i >= 0; i--) {
                let b = bubbles[i];
                b.x += b.speedX + Math.sin(time * 0.002 + b.drift) * 0.5;
                b.y += b.speedY;
                b.opacity -= 0.005;
                if (b.opacity <= 0) {
                    bubbles.splice(i, 1);
                    continue;
                }
                ctx.save();
                ctx.globalAlpha = b.opacity;
                ctx.fillStyle = '#065f46';
                ctx.font = `bold ${b.size}px 'Inter', sans-serif`;
                ctx.fillText(b.char, b.x, b.y);
                ctx.restore();
            }
            requestAnimationFrame(animate);
        }
        requestAnimationFrame(animate);
    </script>
</body>

</html>
