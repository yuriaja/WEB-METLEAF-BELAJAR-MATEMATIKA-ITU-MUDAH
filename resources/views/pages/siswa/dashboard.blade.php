@extends('layouts.app')

@section('title', 'Dashboard Siswa | MathLeaf')
@section('header_title', 'Dashboard Siswa')

@section('content')
    <style>
        .app-content {
            font-size: 11.2px;
        }


        :root {
            --ml-green: #086534;
            --ml-green-hover: #064d27;
            --ml-green-light: #edf8f0;
        }

        .page-heading {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .page-heading h1 {
            font-size: 1.44rem;
            color: #0f172a;
            margin: 0.5rem 0;
        }

        .page-heading p {
            color: #64748b;
            font-size: 0.8rem;
            margin: 0;
        }

        .badge-date {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: var(--ml-green-light);
            color: var(--ml-green);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.68rem;
        }

        /* BANNER DINAMIS & CANVAS INTERAKTIF */
        .dashboard-banner {
            background: var(--ml-green-light);
            /* Latar hijau terang menyatu dengan tema */
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            overflow: hidden;
            position: relative;
            min-height: 220px;
            box-shadow: 0 4px 6px rgba(8, 101, 52, 0.05);
        }

        .banner-text-content {
            z-index: 2;
            position: relative;
            max-width: 60%;
        }

        .banner-text-content h2 {
            color: var(--ml-green-hover);
            /* Teks judul hijau tua */
            font-size: 1.44rem;
            margin: 0.5rem 0 1rem 0;
            line-height: 1.3;
        }

        .banner-text-content p {
            color: #334155;
            /* Teks deskripsi gelap agar mudah dibaca */
            font-size: 0.8rem;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }

        .btn-primary {
            background: var(--ml-green);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            font-size: 0.76rem;
            transition: background 0.2s;
            position: relative;
            z-index: 2;
        }

        .btn-primary:hover {
            background: var(--ml-green-hover);
        }

        /* Canvas Tumbuhan 3D */
        #dashboardPlantCanvas {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 40%;
            height: 100%;
            z-index: 1;
            cursor: pointer;
        }

        /* Overlay Notifikasi Angka di atas Tanaman */
        .notif-badge-overlay {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #ef4444;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
            z-index: 2;
            cursor: pointer;
            animation: bounce 2s infinite;
            display: none;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        /* Pop-up Notifikasi Kustom */
        .custom-popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.5);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s;
        }

        .custom-popup-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .custom-popup-box {
            background: white;
            border-radius: 12px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transform: translateY(20px);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
        }

        .custom-popup-overlay.active .custom-popup-box {
            transform: translateY(0);
        }

        .popup-header {
            background: #f8fafc;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .popup-header h3 {
            margin: 0;
            font-size: 0.88rem;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .popup-close {
            background: transparent;
            border: none;
            cursor: pointer;
            color: #64748b;
        }

        .popup-body {
            padding: 1.5rem;
            text-align: center;
        }

        .popup-body p {
            color: #475569;
            line-height: 1.6;
            margin: 0;
        }

        .notif-list-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid var(--ml-green);
            padding: 1rem;
            border-radius: 6px;
            text-align: left;
            margin-bottom: 0.75rem;
        }

        .notif-list-item strong {
            display: block;
            color: #0f172a;
            margin-bottom: 0.2rem;
        }

        .notif-list-item small {
            color: #64748b;
            font-size: 0.64rem;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
        }

        .stat-icon {
            background: var(--ml-green-light);
            color: var(--ml-green);
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-content {
            display: flex;
            flex-direction: column;
        }

        .stat-label {
            font-size: 0.68rem;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .stat-value {
            font-size: 1.2rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1;
            margin-bottom: 0.25rem;
        }

        .stat-desc {
            font-size: 0.6rem;
            color: #94a3b8;
        }

        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 640px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .banner-text-content {
                max-width: 100%;
            }

            #dashboardPlantCanvas {
                opacity: 0.3;
                width: 100%;
            }
        }

        /* Bottom Columns */
        .dashboard-columns {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 900px) {
            .dashboard-columns {
                grid-template-columns: 1fr;
            }
        }

        .panel {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
        }

        .panel-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .panel-head h2 {
            font-size: 1rem;
            margin: 0;
            color: #0f172a;
        }

        .activity-row {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .activity-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .activity-row strong {
            display: block;
            color: #0f172a;
            margin-bottom: 0.2rem;
            font-size: 0.76rem;
        }

        .activity-row small {
            color: #64748b;
            font-size: 0.68rem;
        }

        .schedule-row {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-bottom: 1.25rem;
        }

        .date-block {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            width: 45px;
            height: 45px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #0f172a;
            line-height: 1;
        }

        .date-block small {
            font-size: 0.52rem;
            color: #64748b;
            font-weight: 600;
            margin-top: 0.1rem;
        }

        /* Upgrade 4: statistik dari database dengan desain MathLeaf lama */
        .ml4-insight-bar { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:1rem; margin-bottom:1.4rem; }
        .ml4-insight-bar a { display:flex; gap:.4rem; flex-direction:column; text-decoration:none; background:#fff; border:1px solid #bbf7d0; border-radius:10px; padding:.9rem 1rem; color:#086534; }
        .ml4-insight-bar strong { font-size:1.5rem; }
        .ml4-insight-bar span { color:#475569; font-weight:600; font-size:.76rem; }
        .ml4-empty-banner { display:flex; flex-direction:column; gap:.4rem; padding:1rem 1.25rem; margin-bottom:1rem; border:1px solid #bbf7d0; border-radius:10px; background:#edf8f0; font-size:.85rem; color:#334155; }
        .ml4-empty-banner strong { color:#064d27; }
        .ml4-empty-banner a,.ml4-more,.ml4-task-link { text-decoration:none; color:#086534; font-weight:700; }
        .ml4-more { font-size:.75rem; }
        .ml4-activity-row a { color:#0f172a; text-decoration:none; }
        .ml4-empty { color:#64748b; font-size:.8rem; padding:.8rem 0; }
        .ml4-gap-top { margin-top:1rem; }
        .ml4-material-row { display:grid; grid-template-columns:minmax(0,1fr) auto; gap:.4rem; padding:.65rem 0; border-bottom:1px solid #edf2f7; align-items:center; }
        .ml4-material-row a { color:#064d27; font-weight:650; text-decoration:none; overflow-wrap:anywhere; }
        .ml4-material-row span { color:#475569; font-size:.8rem; }
        .ml4-progress-track { height:7px; background:#d1fae5; border-radius:8px; overflow:hidden; grid-column:span 2; }
        .ml4-progress-track > div { height:100%; background:#086534; border-radius:8px; }
        .ml4-task-link { display:flex; align-items:center; gap:.8rem; color:inherit; padding:.6rem 0; }
        .ml4-task-link strong { display:block; color:#0f172a; }
        .ml4-task-link small { display:block; color:#64748b; margin-top:.3rem; }
        .ml4-overdue { font-size:.78rem; color:#b91c1c; font-weight:650; }
        .ml4-quiz-row { display:flex; align-items:center; justify-content:space-between; gap:.75rem; padding:.75rem 0; border-bottom:1px solid #edf2f7; }
        .ml4-quiz-row strong { display:block; color:#0f172a; font-size:.83rem; }
        .ml4-quiz-row small { display:block; color:#64748b; font-size:.74rem; margin-top:.2rem; }
        .ml4-quiz-row a { color:#086534; font-size:.75rem; text-decoration:none; font-weight:700; white-space:nowrap; }
        @media(max-width:620px){ .ml4-insight-bar{gap:.5rem;} .ml4-insight-bar a{padding:.7rem;} .ml4-insight-bar strong{font-size:1.15rem;} .ml4-insight-bar span{font-size:.65rem;} }
    </style>

    <div class="page-heading">
        <div>
            <span class="eyebrow" style="color:#086534;font-weight:700;letter-spacing:.05em;text-transform:uppercase;">RUANG BERTUMBUHMU</span>
            <h1>Halo, {{ auth()->user()->name }}! Selamat Datang!</h1>
            <p>
                @if($activeClass)
                    Ringkasan pembelajaran kelas {{ $activeClass->name }}.
                @else
                    Ringkasan pembelajaran dari kelas yang kamu ikuti.
                @endif
            </p>
        </div>
        <span class="badge-date">{{ now()->translatedFormat('l, d F Y') }}</span>
    </div>

    @php
        $unreadCount = $pendingCount + $remedialQuizzes->count();
        $bannerTitle = $unreadCount > 0
            ? "Ada {$unreadCount} hal yang perlu kamu kerjakan."
            : 'Semua tugas dan remedialmu sudah beres!';
        $bannerDesc = $unreadCount > 0
            ? 'Cek tugas yang belum dikumpulkan dan kuis yang bisa diremedial. Klik tanaman untuk melihat rinciannya.'
            : 'Lanjutkan belajar dengan membaca materi atau mengerjakan kuis baru.';
    @endphp

    <section class="dashboard-banner">
        <div class="banner-text-content">
            <span class="eyebrow" style="color:#086534;font-size:.68rem;font-weight:700;letter-spacing:.05em;">MATHLEAF · AKTIVITASMU</span>
            <h2>{{ $bannerTitle }}</h2>
            <p>{{ $bannerDesc }}</p>
            <a class="btn-primary" href="{{ $pendingCount ? route('classroom.tasks.index') : route('materi.index') }}">
                {{ $pendingCount ? 'Lihat Tugas' : 'Lanjut Belajar' }} →
            </a>
        </div>
        <canvas id="dashboardPlantCanvas" onclick="openInteractivePopup()" title="Klik untuk melihat aktivitas belajar"></canvas>
        <div id="notifBadge" class="notif-badge-overlay" onclick="openInteractivePopup()">{{ $unreadCount }}</div>
    </section>

    <div id="interactive-popup" class="custom-popup-overlay" role="dialog" aria-modal="true" aria-label="Aktivitas yang perlu dikerjakan">
        <div class="custom-popup-box">
            <div class="popup-header">
                <h3>Aktivitas MathLeaf</h3>
                <button type="button" class="popup-close" onclick="closeInteractivePopup()" aria-label="Tutup">✕</button>
            </div>
            <div class="popup-body">
                @if($unreadCount === 0)
                    <p>Belum ada tugas tertunda atau kuis remedial. Terus belajar, satu langkah setiap hari! 🌱</p>
                @else
                    @foreach($pendingTasks->take(4) as $task)
                        <div class="notif-list-item">
                            <strong>Tugas: {{ $task->title }}</strong>
                            <small>{{ $task->due_at ? 'Deadline: '.$task->due_at->translatedFormat('d M Y H:i') : 'Tanpa deadline' }}</small>
                        </div>
                    @endforeach
                    @foreach($remedialQuizzes->take(3) as $quiz)
                        <div class="notif-list-item">
                            <strong>Remedial: {{ $quiz->title }}</strong>
                            <small>Sisa kesempatan: {{ max(0, (int)$quiz->max_attempts - $quiz->attempts->count()) }}</small>
                        </div>
                    @endforeach
                    <a class="btn-primary" style="width:100%;justify-content:center;margin-top:.6rem;" href="{{ route('classroom.tasks.index') }}">Buka Daftar Tugas →</a>
                @endif
            </div>
        </div>
    </div>

    @if($classes->isEmpty())
        <div class="ml4-empty-banner">
            <strong>Belum ada kelas yang diikuti.</strong>
            Gabung kelas menggunakan kode dari guru agar materi, tugas, dan nilai muncul di sini.
            <a href="{{ route('classroom.home') }}">Gabung kelas →</a>
        </div>
    @endif

    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-icon">🌱</span>
            <div class="stat-content">
                <span class="stat-label">Progres Materi</span>
                <span class="stat-value">{{ $materialProgress }}%</span>
                <span class="stat-desc">{{ $materialsCompleted }} dari {{ $materials->count() }} materi selesai</span>
            </div>
        </div>
        <div class="stat-card">
            <span class="stat-icon" style="background:#e0f2fe;color:#0284c7;">✓</span>
            <div class="stat-content">
                <span class="stat-label">Tugas Dikumpulkan</span>
                <span class="stat-value">{{ $submittedCount }}</span>
                <span class="stat-desc">dari {{ $tasks->count() }} tugas</span>
            </div>
        </div>
        <div class="stat-card">
            <span class="stat-icon" style="background:#fef2f2;color:#dc2626;">!</span>
            <div class="stat-content">
                <span class="stat-label">Tugas Belum Selesai</span>
                <span class="stat-value">{{ $pendingCount }}</span>
                <span class="stat-desc">{{ $overdueCount }} sudah melewati deadline</span>
            </div>
        </div>
        <div class="stat-card">
            <span class="stat-icon" style="background:#fef9c3;color:#ca8a04;">★</span>
            <div class="stat-content">
                <span class="stat-label">Rata-rata Nilai</span>
                <span class="stat-value">{{ $averageScore === null ? '—' : number_format($averageScore, 1, ',', '.') }}</span>
                <span class="stat-desc">Nilai kuis terbaru dan tugas dinilai</span>
            </div>
        </div>
    </div>

    <div class="ml4-insight-bar">
        <a href="{{ route('siswa.kuis') }}">
            <strong>{{ $availableQuizzes->count() }}</strong><span>Kuis tersedia</span>
        </a>
        <a href="{{ route('siswa.kuis') }}">
            <strong>{{ $remedialQuizzes->count() }}</strong><span>Remedial tersedia</span>
        </a>
        <a href="{{ route('materi.index') }}">
            <strong>{{ $materials->count() }}</strong><span>Materi terbit</span>
        </a>
    </div>

    <div class="dashboard-columns">
        <div>
            <section class="panel">
                <div class="panel-head">
                    <h2>Aktivitas Belajarmu</h2>
                    <a class="ml4-more" href="{{ route('siswa.nilai') }}">Lihat nilai →</a>
                </div>
                @forelse($recentActivities as $activity)
                    <div class="activity-row ml4-activity-row">
                        <span class="stat-icon" style="width:32px;height:32px;">✓</span>
                        <div>
                            <a href="{{ $activity->url }}"><strong>{{ $activity->title }}</strong></a>
                            <small>{{ $activity->detail }} · {{ $activity->date?->diffForHumans() ?? '-' }}</small>
                        </div>
                    </div>
                @empty
                    <p class="ml4-empty">Belum ada aktivitas belajar. Mulailah dari materi atau tugas kelas.</p>
                @endforelse
            </section>

            <section class="panel ml4-gap-top">
                <div class="panel-head">
                    <h2>Materi untuk Dilanjutkan</h2>
                    <a class="ml4-more" href="{{ route('materi.index') }}">Semua materi →</a>
                </div>
                @forelse($incompleteMaterials as $material)
                    @php $percent = (int)($progressById[$material->id]->progress_percent ?? 0); @endphp
                    <div class="ml4-material-row">
                        <a href="{{ route('materi.show', $material) }}">{{ $material->title }}</a>
                        <span>{{ $percent }}%</span>
                        <div class="ml4-progress-track"><div style="width:{{ $percent }}%"></div></div>
                    </div>
                @empty
                    <p class="ml4-empty">Belum ada materi yang perlu dilanjutkan.</p>
                @endforelse
            </section>
        </div>

        <div>
            <section class="panel">
                <div class="panel-head">
                    <h2>Deadline Tugas Terdekat</h2>
                    <a class="ml4-more" href="{{ route('classroom.tasks.index') }}">Lihat tugas →</a>
                </div>
                @forelse($upcomingTasks as $task)
                    <a class="schedule-row ml4-task-link" href="{{ route('classroom.tasks.index') }}">
                        <div class="date-block">{{ $task->due_at->format('d') }}<small>{{ strtoupper($task->due_at->translatedFormat('M')) }}</small></div>
                        <div><strong>{{ $task->title }}</strong><small>Deadline {{ $task->due_at->translatedFormat('d M Y, H:i') }}</small></div>
                    </a>
                @empty
                    <p class="ml4-empty">Belum ada deadline tugas mendatang.</p>
                @endforelse
                @if($overdueCount > 0)
                    <p class="ml4-overdue">{{ $overdueCount }} tugas belum dikumpulkan dan sudah melewati deadline.</p>
                @endif
            </section>
            <section class="panel ml4-gap-top">
                <div class="panel-head">
                    <h2>Kuis & Remedial</h2>
                    <a class="ml4-more" href="{{ route('siswa.kuis') }}">Lihat semua →</a>
                </div>
                @forelse($availableQuizzes->take(4) as $quiz)
                    <div class="ml4-quiz-row">
                        <div><strong>{{ $quiz->title }}</strong><small>{{ $quiz->attempts->count() > 0 ? 'Remedial' : 'Kuis baru' }} · KKM {{ $quiz->passing_score ?? 70 }}</small></div>
                        <a href="{{ route('classroom.quiz.take', $quiz) }}">Kerjakan →</a>
                    </div>
                @empty
                    <p class="ml4-empty">Belum ada kuis yang perlu dikerjakan.</p>
                @endforelse
            </section>
            <section class="panel ml4-gap-top">
                <div class="panel-head">
                    <h2>Diskusi Kelas Terbaru</h2>
                    <a class="ml4-more" href="{{ route('forum.index') }}">Buka forum →</a>
                </div>
                @forelse($recentForumThreads as $thread)
                    <div class="ml4-quiz-row">
                        <div>
                            <a href="{{ route('forum.index', ['class_id' => $thread->class_id], false) }}"><strong>{{ $thread->title }}</strong></a>
                            <small>{{ $thread->schoolClass?->name ?? 'Kelas' }} · {{ $thread->created_at?->diffForHumans() }}
                                @if($thread->is_pinned) · Disematkan @endif
                                @if($thread->is_locked) · Dikunci @endif
                            </small>
                        </div>
                    </div>
                @empty
                    <p class="ml4-empty">Belum ada diskusi kelas. Kamu bisa memulai pertanyaan baru di forum.</p>
                @endforelse
            </section>
        </div>
    </div>

    <!-- SCRIPTS: Logic Tumbuhan 3D Math Plant Khusus Banner -->
    <script>
        // POP-UP LOGIC
        function openInteractivePopup() {
            document.getElementById('interactive-popup').classList.add('active');
        }

        function closeInteractivePopup() {
            document.getElementById('interactive-popup').classList.remove('active');
        }
        document.getElementById('interactive-popup').addEventListener('click', function(e) {
            if (e.target === this) closeInteractivePopup();
        });

        // CANVAS ANIMATION LOGIC
        const notifCount = {{ $unreadCount }};
        const canvas = document.getElementById('dashboardPlantCanvas');
        const ctx = canvas.getContext('2d');

        // Tampilkan badge angka di canvas jika > 0
        if (notifCount > 0) {
            document.getElementById('notifBadge').style.display = 'flex';
        }

        let w, h;

        function resize() {
            w = canvas.width = canvas.offsetWidth;
            h = canvas.height = canvas.offsetHeight;
        }
        window.addEventListener('resize', resize);
        resize();

        const leaves = [];
        const bubbles = [];
        const mathChars = ['+', '−', '×', '÷', '=', '%', '½'];

        let rootX = w * 0.7; // Akar tanaman berada sedikit ke kanan
        let rootY = h + 20;

        function addLeaf(targetX, targetY) {
            let baseAng = (Math.random() * Math.PI) - (Math.random() * Math.PI);
            leaves.push({
                x: targetX,
                y: targetY,
                baseAngle: baseAng,
                angle: baseAng,
                scale: 0,
                maxScale: Math.random() * 0.5 + 0.5,
                growthRate: 0.05,
                swaySpeed: Math.random() * 0.0015 + 0.001,
                swayOffset: Math.random() * Math.PI * 2,
                swayAmount: Math.random() * 0.15 + 0.05
            });
            spawnBubble(targetX, targetY);
        }

        function spawnBubble(x, y) {
            bubbles.push({
                x: x + (Math.random() * 40 - 20),
                y: y + (Math.random() * 40 - 20),
                char: mathChars[Math.floor(Math.random() * mathChars.length)],
                speedY: -(Math.random() * 1.5 + 0.5),
                speedX: (Math.random() - 0.5) * 1.0,
                opacity: 1,
                size: Math.random() * 10 + 10,
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

        // Inisialisasi: Jika ada notif, tumbuhkan daun sesuai jumlah notif (max 6)
        if (notifCount > 0) {
            let maxLeaves = Math.min(notifCount, 6);
            for (let i = 0; i < maxLeaves; i++) {
                setTimeout(() => {
                    let ranX = (w * 0.5) + (Math.random() * (w * 0.3));
                    let ranY = (h * 0.6) - (Math.random() * (h * 0.4));
                    addLeaf(ranX, ranY);
                }, i * 300);
            }
        }

        function animate(time) {
            ctx.clearRect(0, 0, w, h);

            if (notifCount > 0) {
                // Gambar Akar
                rootX = w * 0.6;
                ctx.strokeStyle = '#6ee7b7';
                ctx.lineWidth = 3;
                ctx.lineCap = 'round';
                let rootSway = Math.sin(time * 0.0005) * 10;

                leaves.forEach(leaf => {
                    leaf.angle = leaf.baseAngle + Math.sin(time * leaf.swaySpeed + leaf.swayOffset) * leaf
                        .swayAmount;
                    ctx.beginPath();
                    ctx.moveTo(rootX + rootSway, rootY);
                    ctx.quadraticCurveTo(rootX, leaf.y + 50, leaf.x, leaf.y);
                    ctx.stroke();
                });

                leaves.forEach(leaf => {
                    if (leaf.scale < leaf.maxScale) leaf.scale += leaf.growthRate;
                    drawLeaf(leaf);
                    if (Math.random() < 0.005) spawnBubble(leaf.x, leaf.y);
                });

                for (let i = bubbles.length - 1; i >= 0; i--) {
                    let b = bubbles[i];
                    b.x += b.speedX + Math.sin(time * 0.002 + b.drift) * 0.5;
                    b.y += b.speedY;
                    b.opacity -= 0.008;
                    if (b.opacity <= 0) {
                        bubbles.splice(i, 1);
                        continue;
                    }

                    ctx.save();
                    ctx.globalAlpha = b.opacity;
                    ctx.fillStyle = '#6ee7b7'; // Warna text hijau terang
                    ctx.font = `bold ${b.size}px 'Inter', sans-serif`;
                    ctx.fillText(b.char, b.x, b.y);
                    ctx.restore();
                }
            }
            requestAnimationFrame(animate);
        }
        requestAnimationFrame(animate);
    </script>
@endsection
