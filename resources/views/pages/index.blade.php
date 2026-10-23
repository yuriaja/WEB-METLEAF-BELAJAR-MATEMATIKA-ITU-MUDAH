<!doctype html>
<html lang="id">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Portal Masuk | MathLeaf</title>
    <meta name="description" content="MathLeaf — Grow Your Math Skills. Portal masuk siswa kelas VII SMP." />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f0fdf4;
            /* emerald-50 */
        }

        /* Glassmorphism Panel Kanan */
        .glass-panel {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border-left: 1px solid rgba(255, 255, 255, 0.9);
            box-shadow: -20px 0 60px -15px rgba(22, 163, 74, 0.2);
        }

        /* Input 6 Kotak (Kode Kelas) */
        .code-box {
            text-align: center;
            text-transform: uppercase;
            font-size: 1.5rem;
            font-weight: 900;
            background: rgba(255, 255, 255, 0.8);
            border: 2px solid #bbf7d0;
            /* emerald-200 */
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .code-box:focus {
            outline: none;
            border-color: #10b981;
            /* emerald-500 */
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2);
            transform: translateY(-2px);
        }

        /* Efek Hover Tombol */
        .btn-math {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-math:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(22, 163, 74, 0.3);
        }

        /* Custom glow untuk teks agar terbaca tanpa kotak background */
        .title-glow {
            text-shadow:
                -2px -2px 0 #fff,
                2px -2px 0 #fff,
                -2px 2px 0 #fff,
                2px 2px 0 #fff,
                0 15px 35px rgba(255, 255, 255, 0.9);
        }

        .caption-glow {
            text-shadow: 0 2px 15px rgba(255, 255, 255, 1), 0 0 30px rgba(255, 255, 255, 0.9);
        }
    </style>
</head>

<body class="h-screen w-full overflow-hidden flex flex-col items-center justify-center relative">
    <!-- Canvas Background: Interactive Math Plant -->
    <canvas id="mathPlantCanvas" class="absolute inset-0 w-full h-full z-0 cursor-pointer"></canvas>

    <!-- Konten Utama (Tengah) -->
    <div
        class="z-10 flex flex-col items-center justify-center text-center w-full max-w-3xl px-6 relative pointer-events-none">

        <!-- Slogan MathLeaf -->
        <div
            class="flex items-center gap-3 mb-6 text-emerald-700 font-bold tracking-widest text-xs uppercase bg-white/50 px-5 py-2 rounded-full border border-emerald-200/50 backdrop-blur-md shadow-sm">
            <span class="relative flex h-2 w-2">
                <span
                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            Grow Your Math Skills
        </div>

        <!-- Logo & Judul -->
        <div class="flex flex-col md:flex-row items-center justify-center gap-4 lg:gap-6 mb-6">
            <div
                class="w-20 h-20 md:w-24 md:h-24 bg-gradient-to-tr from-emerald-500 to-green-400 rounded-3xl flex items-center justify-center text-white shadow-xl shadow-emerald-500/40 rotate-3 border-2 border-white">
                <svg width="40" height="40" class="md:w-14 md:h-14" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 20A7 7 0 0 1 4 13C4 5 14 3 21 3c0 7-2 17-10 17Z" />
                    <path d="M4 21c0-6 5-11 11-14" />
                </svg>
            </div>
            <h1 class="text-6xl md:text-7xl lg:text-8xl font-black text-emerald-950 tracking-tighter title-glow">
                Math<span class="text-emerald-500">Leaf</span>
            </h1>
        </div>

        <!-- Caption -->
        <p class="text-lg md:text-xl text-emerald-900 font-medium leading-relaxed max-w-xl mb-10 caption-glow mx-auto">
            Matematika jadi lebih menyenangkan. Pecahkan angka, dan lihat pemahamanmu bertumbuh subur!
        </p>

        <!-- Tombol Aksi (Masuk & Daftar) -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pointer-events-auto">
            <a href="{{ url('/masuk') }}"
                class="btn-math w-full sm:w-auto bg-emerald-600 text-white font-bold py-3.5 px-8 rounded-2xl flex items-center justify-center gap-3 shadow-lg shadow-emerald-600/30 hover:bg-emerald-700 hover:scale-105 transition-all">
                Masuk
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                    <polyline points="10 17 15 12 10 7" />
                    <line x1="15" y1="12" x2="3" y2="12" />
                </svg>
            </a>

            <a href="{{ url('/daftar') }}"
                class="btn-math w-full sm:w-auto bg-white/70 text-emerald-800 font-bold py-3.5 px-8 rounded-2xl border-2 border-emerald-200 hover:bg-white hover:border-emerald-400 hover:scale-105 transition-all text-center flex items-center justify-center gap-2 backdrop-blur-sm">
                Daftar Akun Baru
            </a>
        </div>

        <!-- Tombol Tumbuhkan Daun -->
        <div class="mt-8 inline-flex items-center gap-2 bg-emerald-600/90 backdrop-blur-sm px-6 py-3 rounded-full shadow-lg shadow-emerald-500/20 text-sm font-medium text-white pointer-events-auto hover:bg-emerald-700 hover:scale-105 transition-all cursor-pointer"
            onclick="forceAddLeaf(event)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M15.5 3H5a2 2 0 0 0-2 2v14c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2V8.5L15.5 3Z" />
                <path d="M14 3v7h7" />
            </svg>
            Klik untuk menumbuhkan daun
        </div>

    </div>
    <!-- Script Logic Kotak Kode 6 Digit -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const inputs = document.querySelectorAll('.code-box');
            const hiddenInput = document.getElementById('hidden_class_code');

            inputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    updateHiddenCode();
                    if (e.target.value.length === 1 && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                });

                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
                        inputs[index - 1].focus();
                    }
                });

                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pastedData = e.clipboardData.getData('text').replace(/[^a-zA-Z0-9]/g, '')
                        .slice(0, 6).toUpperCase();
                    for (let i = 0; i < pastedData.length; i++) {
                        if (inputs[i]) {
                            inputs[i].value = pastedData[i];
                        }
                    }
                    if (pastedData.length > 0 && pastedData.length < 6) {
                        inputs[pastedData.length].focus();
                    } else if (pastedData.length === 6) {
                        inputs[5].focus();
                    }
                    updateHiddenCode();
                });
            });

            function updateHiddenCode() {
                let code = '';
                inputs.forEach(input => code += input.value);
                hiddenInput.value = code;
            }
        });
    </script>

    <!-- Script Logic Interactive 3D Math Plant -->
    <script>
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

            // Mengurangi ledakan angka saat daun pertama kali muncul
            for (let i = 0; i < 1; i++) {
                spawnBubble(targetX, targetY);
            }
        }

        // Setup awal 8 daun dekorasi
        for (let i = 0; i < 8; i++) {
            setTimeout(() => {
                let ranX = w * 0.15 + (Math.random() * (w * 0.4) - (w * 0.2));
                let ranY = h * 0.8 - (Math.random() * (h * 0.6));
                addLeaf(ranX, ranY);
            }, i * 250);
        }

        window.forceAddLeaf = function(e) {
            e.stopPropagation();
            let ranX = w * 0.15 + (Math.random() * (w * 0.4) - (w * 0.2));
            let ranY = h * 0.8 - (Math.random() * (h * 0.6));
            addLeaf(ranX, ranY);
        }

        canvas.addEventListener('click', (e) => {
            addLeaf(e.clientX, e.clientY);
        });

        function spawnBubble(x, y) {
            bubbles.push({
                x: x + (Math.random() * 40 - 20),
                y: y + (Math.random() * 40 - 20),
                char: mathChars[Math.floor(Math.random() * mathChars.length)],
                speedY: -(Math.random() * 1.5 + 0.5), // Bergerak lebih santai
                speedX: (Math.random() - 0.5) * 1.0,
                opacity: 1,
                size: Math.random() * 12 + 12,
                drift: Math.random() * Math.PI * 2
            });
        }

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

                // Peluang sangat kecil (0.3%) daun menghasilkan oksigen angka agar lebih minimalis
                if (Math.random() < 0.003) {
                    spawnBubble(leaf.x, leaf.y);
                }
            });

            for (let i = bubbles.length - 1; i >= 0; i--) {
                let b = bubbles[i];
                b.x += b.speedX + Math.sin(time * 0.002 + b.drift) * 0.5;
                b.y += b.speedY;
                b.opacity -= 0.005; // Fade out lebih cepat

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
