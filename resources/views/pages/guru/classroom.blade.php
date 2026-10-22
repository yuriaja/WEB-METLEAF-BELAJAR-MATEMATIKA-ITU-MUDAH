@extends('layouts.app')

@section('title', 'Manajemen Kelas | MathLeaf')
@section('header_title', 'Manajemen Kelas')

@section('content')
    <style>
        /* Variabel Warna Hijau MathLeaf */
        :root {
            --ml-green: #086534;
            --ml-green-hover: #064d27;
            --ml-green-light: #edf8f0;
            --ml-green-border: #bbf0cb;
        }

        /* Header & Typography */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.02em;
        }

        /* Modern Buttons dengan Tema Hijau */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
            font-weight: 500;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s ease;
            border: 1px solid transparent;
            text-decoration: none;
        }

        .btn-primary {
            background-color: var(--ml-green);
            color: #ffffff;
            border-color: var(--ml-green);
        }

        .btn-primary:hover {
            background-color: var(--ml-green-hover);
            box-shadow: 0 4px 6px rgba(8, 101, 52, 0.2);
        }

        .btn-outline {
            background-color: #ffffff;
            border-color: #e2e8f0;
            color: #334155;
        }

        .btn-outline:hover {
            background-color: #f8fafc;
            border-color: #cbd5e1;
        }

        .btn-danger-ghost {
            background-color: transparent;
            color: #ef4444;
            padding: 0.4rem 0.6rem;
        }

        .btn-danger-ghost:hover {
            background-color: #fef2f2;
            color: #dc2626;
        }

        /* Class Cards List */
        .class-feed {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .class-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);
            overflow: hidden;
        }

        .class-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            background: #ffffff;
        }

        /* Layout Kiri (Kode + Nama) */
        .header-left-group {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        /* Kotak Kode Kelas Besar */
        .class-code-box {
            background-color: var(--ml-green-light);
            border: 1px solid var(--ml-green-border);
            border-radius: 6px;
            padding: 0.75rem 1rem;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            min-width: 140px;
        }

        .code-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--ml-green);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.2rem;
        }

        .code-value-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .code-text {
            font-family: monospace;
            font-size: 1.4rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: 0.05em;
        }

        .btn-copy {
            background: transparent;
            border: none;
            color: var(--ml-green);
            cursor: pointer;
            padding: 0.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: background 0.15s;
        }

        .btn-copy:hover {
            background: rgba(8, 101, 52, 0.1);
        }

        .class-info h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #0f172a;
            margin: 0 0 0.3rem 0;
            letter-spacing: -0.01em;
        }

        .class-info p {
            font-size: 0.95rem;
            color: #64748b;
            margin: 0;
        }

        .class-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .chevron-icon {
            transition: transform 0.2s ease;
        }

        /* Dropdown Siswa */
        .class-detail {
            border-top: 1px solid #f1f5f9;
            background: #f8fafc;
            padding: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
        }

        .class-detail.open {
            max-height: 2000px;
            padding: 1.5rem;
        }

        .detail-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0 0 1rem 0;
        }

        .student-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .student-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 0.75rem 1rem;
            border-radius: 6px;
        }

        .student-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .student-avatar {
            width: 32px;
            height: 32px;
            background: var(--ml-green-light);
            color: var(--ml-green);
            border: 1px solid var(--ml-green-border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .student-name {
            font-size: 0.95rem;
            font-weight: 500;
            color: #1e293b;
        }

        .student-email {
            font-size: 0.85rem;
            color: #64748b;
        }

        /* Modal Pop-up */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(2px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }

        .modal-backdrop.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-box {
            background: #ffffff;
            width: 100%;
            max-width: 420px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transform: translateY(10px);
            transition: transform 0.2s ease;
        }

        .modal-backdrop.active .modal-box {
            transform: translateY(0);
        }

        .modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 1.15rem;
            font-weight: 600;
            color: #0f172a;
        }

        .modal-close {
            background: transparent;
            border: none;
            color: #64748b;
            cursor: pointer;
            padding: 0.25rem;
            display: flex;
        }

        .modal-close:hover {
            color: #0f172a;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: 500;
            color: #334155;
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 0.65rem 0.8rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 0.95rem;
            color: #0f172a;
            transition: border-color 0.15s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--ml-green);
        }

        .modal-footer {
            padding: 1.25rem 1.5rem;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            background: #f8fafc;
            border-radius: 0 0 8px 8px;
        }
    </style>

    <!-- Header & Aksi Utama -->
    <div class="page-header">
        <h1>Daftar Kelas yang Diajar</h1>
        <button type="button" class="btn btn-primary" onclick="openModal()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Tambah Kelas Baru
        </button>
    </div>

    <!-- Modal Tambah Kelas -->
    <div id="modal-tambah" class="modal-backdrop">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Buat Kelas Baru</h3>
                <button type="button" class="modal-close" onclick="closeModal()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <form action="{{ route('classroom.create') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nama Kelas</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Contoh: VII A"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="school_year">Tahun Ajaran</label>
                        <input type="text" id="school_year" name="school_year" class="form-control"
                            placeholder="Contoh: 2026/2027">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kelas</button>
                </div>
            </form>
        </div>
    </div>

    <!-- List Kelas -->
    <div class="class-feed">
        @forelse($classes ?? [] as $class)
            <div class="class-card">
                <!-- Info Kelas Utama -->
                <div class="class-card-header">
                    <div class="header-left-group">
                        <!-- Kode Kelas Besar di Kiri -->
                        <div class="class-code-box">
                            <span class="code-label">Kode Kelas</span>
                            <div class="code-value-group">
                                <span class="code-text">{{ $class->join_code }}</span>
                                <!-- Tombol Salin Kode -->
                                <button type="button" class="btn-copy" onclick="copyCode('{{ $class->join_code }}', this)"
                                    title="Salin Kode">
                                    <svg class="icon-copy" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2">
                                        </rect>
                                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Nama Kelas dan Info -->
                        <div class="class-info">
                            <h2>{{ $class->name }}</h2>
                            <p>{{ $class->school_year ?? 'Tahun Ajaran Belum Diisi' }}</p>
                        </div>
                    </div>

                    <!-- Aksi Kanan -->
                    <div class="class-actions">
                        <button class="btn btn-outline" onclick="toggleDetail({{ $class->id }})">
                            Lihat Detail
                            <svg id="icon-{{ $class->id }}" class="chevron-icon" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>

                        <!-- Tombol Hapus Kelas -->
                        <button class="btn btn-danger-ghost" onclick="alert('Route Hapus Kelas belum dibuat!')"
                            title="Hapus Kelas">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Dropdown Siswa -->
                <div id="detail-{{ $class->id }}" class="class-detail">
                    <h4 class="detail-title">Daftar Anggota Siswa</h4>
                    <div class="student-list">
                        @forelse($class->students ?? [] as $student)
                            <div class="student-row">
                                <div class="student-info">
                                    <div class="student-avatar">
                                        {{ strtoupper(substr($student->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="student-name">{{ $student->name }}</div>
                                        <div class="student-email">{{ $student->email }}</div>
                                    </div>
                                </div>
                                <button class="btn btn-danger-ghost"
                                    onclick="alert('Route Keluarkan Siswa belum dibuat!')" title="Keluarkan Siswa">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="15" y1="9" x2="9" y2="15"></line>
                                        <line x1="9" y1="9" x2="15" y2="15"></line>
                                    </svg>
                                </button>
                            </div>
                        @empty
                            <div style="color: #64748b; font-size: 0.9rem; text-align: center; padding: 1rem 0;">
                                Belum ada siswa yang bergabung di kelas ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @empty
            <!-- State Kosong -->
            <div
                style="background: #ffffff; border: 1px dashed var(--ml-green-border); border-radius: 8px; padding: 3rem; text-align: center;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--ml-green)"
                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 1rem;">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="3" y1="9" x2="21" y2="9"></line>
                    <line x1="9" y1="21" x2="9" y2="9"></line>
                </svg>
                <h3 style="margin: 0 0 0.5rem 0; color: #0f172a; font-size: 1.1rem;">Belum Ada Kelas</h3>
                <p style="margin: 0; color: #64748b; font-size: 0.95rem;">Buat kelas pertamamu untuk mulai menambahkan
                    siswa.</p>
            </div>
        @endforelse
    </div>

    <script>
        // Fitur Salin Kode Kelas
        function copyCode(code, buttonElement) {
            navigator.clipboard.writeText(code).then(() => {
                // Simpan ikon awal
                const originalIcon = buttonElement.innerHTML;
                // Ganti jadi ikon checklist
                buttonElement.innerHTML = `
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            `;
                // Kembalikan ke ikon awal setelah 2 detik
                setTimeout(() => {
                    buttonElement.innerHTML = originalIcon;
                }, 2000);
            });
        }

        // Logika Modal Pop-up
        const modal = document.getElementById('modal-tambah');

        function openModal() {
            modal.classList.add('active');
        }

        function closeModal() {
            modal.classList.remove('active');
        }
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });

        // Logika Accordion (Dropdown Detail Siswa)
        function toggleDetail(id) {
            const detailDiv = document.getElementById('detail-' + id);
            const icon = document.getElementById('icon-' + id);
            if (detailDiv.classList.contains('open')) {
                detailDiv.classList.remove('open');
                icon.style.transform = 'rotate(0deg)';
            } else {
                detailDiv.classList.add('open');
                icon.style.transform = 'rotate(180deg)';
            }
        }
    </script>
@endsection
