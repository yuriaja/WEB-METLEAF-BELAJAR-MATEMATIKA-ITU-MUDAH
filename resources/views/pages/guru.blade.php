<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Dashboard Guru | MathLeaf</title>
    <meta
      name="description"
      content="MathLeaf — Grow Your Math Skills. Pembelajaran Matematika kelas VII SMP."
    />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <link rel="stylesheet" href="{{ asset('css/mathleaf.css') }}" />
    <script defer src="{{ asset('js/mathleaf.js') }}"></script>
  </head>
  <body data-page="guru">
    <div class="app-layout" id="app-layout">
      <aside class="app-sidebar">
        <a href="{{ url('/') }}" class="logo logo-light"
          ><span class="logo-mark"
            ><svg
              aria-hidden="true"
              width="23"
              height="23"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              style="flex-shrink: 0"
            >
              <path d="M11 20A7 7 0 0 1 4 13C4 5 14 3 21 3c0 7-2 17-10 17Z" />
              <path d="M4 21c0-6 5-11 11-14" /></svg></span
          ><span
            >Math<span class="logo-leaf">Leaf</span
            ><small>GROW YOUR MATH SKILLS</small></span
          ></a
        >
        <div class="sidebar-class">
          <svg
            aria-hidden="true"
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            style="flex-shrink: 0"
          >
            <path d="M7 20h10" />
            <path d="M10 20c5.5-2.5.8-8.3 5-11" />
            <path d="M9 9c-3 0-5-2-5-5 4 0 7 2 7 6" />
            <path d="M14 12c0-4 3-6 7-6 0 5-3 7-7 7" /></svg
          ><span>Ruang Guru<small>Bu Ratna · Pengajar</small></span>
        </div>
        <span class="sidebar-caption">PENGELOLAAN</span>
        <nav>
          <a href="{{ url('/guru') }}" class="selected"
            ><svg
              aria-hidden="true"
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              style="flex-shrink: 0"
            >
              <path d="m2 10 10-5 10 5-10 5-10-5Z" />
              <path d="M6 12v5c3 2 9 2 12 0v-5M22 10v7" /></svg
            ><span>Dashboard Guru</span></a
          ><a href="{{ url('/materi') }}" class=""
            ><svg
              aria-hidden="true"
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              style="flex-shrink: 0"
            >
              <path d="M12 7v14" />
              <path
                d="M3 18V5a2 2 0 0 1 2-2h3a4 4 0 0 1 4 4 4 4 0 0 1 4-4h3a2 2 0 0 1 2 2v13h-5a4 4 0 0 0-4 3 4 4 0 0 0-4-3H3Z"
              /></svg
            ><span>Manajemen Materi</span></a
          ><a href="{{ url('/forum') }}" class=""
            ><svg
              aria-hidden="true"
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              style="flex-shrink: 0"
            >
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
              <circle cx="9" cy="7" r="4" />
              <path
                d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"
              /></svg
            ><span>Forum Diskusi</span></a
          ><a href="{{ url('/tugas') }}" class=""
            ><svg
              aria-hidden="true"
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              style="flex-shrink: 0"
            >
              <path d="M12 7v14" />
              <path
                d="M3 18V5a2 2 0 0 1 2-2h3a4 4 0 0 1 4 4 4 4 0 0 1 4-4h3a2 2 0 0 1 2 2v13h-5a4 4 0 0 0-4 3 4 4 0 0 0-4-3H3Z"
              /></svg
            ><span>Pengumpulan Tugas</span></a
          ><a href="{{ url('/nilai') }}" class=""
            ><svg
              aria-hidden="true"
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              style="flex-shrink: 0"
            >
              <circle cx="12" cy="8" r="6" />
              <path d="m8 13-2 8 6-3 6 3-2-8" /></svg
            ><span>Analitik Nilai</span></a
          >
        </nav>
        <div class="sidebar-bottom">
          <div class="sidebar-motivation">
            <svg
              aria-hidden="true"
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              style="flex-shrink: 0"
            >
              <path d="M7 20h10" />
              <path d="M10 20c5.5-2.5.8-8.3 5-11" />
              <path d="M9 9c-3 0-5-2-5-5 4 0 7 2 7 6" />
              <path d="M14 12c0-4 3-6 7-6 0 5-3 7-7 7" />
            </svg>
            <p>
              Setiap langkah kecil membawa perubahan besar. Terus bertumbuh!
            </p>
          </div>
          <a href="{{ url('/') }}"
            ><svg
              aria-hidden="true"
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              style="flex-shrink: 0"
            >
              <path d="M5 12h14" />
              <path d="m12 5 7 7-7 7" /></svg
            ><span>Ke Beranda</span></a
          ><a href="{{ url('/masuk') }}" id="logout-demo"
            ><svg
              aria-hidden="true"
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              style="flex-shrink: 0"
            >
              <path d="M18 6 6 18M6 6l12 12" /></svg
            ><span>Keluar (demo)</span></a
          >
        </div>
      </aside>
      <div class="app-main">
        <header class="app-header">
          <div style="display: flex; align-items: center; gap: 12px">
            <button
              type="button"
              class="btn btn-ghost"
              id="collapse-sidebar"
              aria-label="Ciutkan sidebar"
            >
              ☰</button
            ><strong>Dashboard Guru</strong>
          </div>
          <div style="display: flex; gap: 16px; align-items: center">
            <button
              type="button"
              class="btn btn-ghost"
              id="notification-button"
              aria-label="Notifikasi"
            >
              <svg
                aria-hidden="true"
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                style="flex-shrink: 0"
              >
                <path
                  d="m12 3 1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3Z"
                />
                <path
                  d="m5 17 .8 2.2L8 20l-2.2.8L5 23l-.8-2.2L2 20l2.2-.8L5 17Z"
                />
                <path
                  d="m19 2 .7 1.3L21 4l-1.3.7L19 6l-.7-1.3L17 4l1.3-.7L19 2Z"
                />
              </svg></button
            ><span class="header-profile"
              ><span class="avatar" id="avatar-letter">A</span
              ><span data-student-name>Aurora</span></span
            >
          </div>
          <div class="notification-panel" id="notification-panel" hidden>
            <strong>Notifikasi MathLeaf</strong>
            <p>Terus belajar, satu langkah kecil setiap hari 🌱</p>
          </div>
        </header>
        <main class="app-content">
          <div class="page-heading">
            <div>
              <span class="eyebrow"
                ><svg
                  aria-hidden="true"
                  width="14"
                  height="14"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  style="flex-shrink: 0"
                >
                  <path
                    d="M11 20A7 7 0 0 1 4 13C4 5 14 3 21 3c0 7-2 17-10 17Z"
                  />
                  <path d="M4 21c0-6 5-11 11-14" />
                </svg>
                RUANG BERTUMBUHMU</span
              >
              <h1>Selamat Datang, Ibu Ratna! 👋</h1>
              <p>
                Bantu setiap siswa bertumbuh melalui pembelajaran yang bermakna.
              </p>
            </div>
            <button class="btn btn-primary" type="button" id="teacher-new">
              + Buat Aktivitas
            </button>
          </div>
          <div class="stats-grid">
            <div class="stat-card">
              <span class="stat-icon"
                ><svg
                  aria-hidden="true"
                  width="18"
                  height="18"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  style="flex-shrink: 0"
                >
                  <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                  <circle cx="9" cy="7" r="4" />
                  <path
                    d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"
                  /></svg
              ></span>
              <div>
                <span class="stat-label">Total Siswa</span><strong>32</strong
                ><small>Kelas VII A</small>
              </div>
            </div>
            <div class="stat-card">
              <span class="stat-icon"
                ><svg
                  aria-hidden="true"
                  width="18"
                  height="18"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  style="flex-shrink: 0"
                >
                  <path d="M7 20h10" />
                  <path d="M10 20c5.5-2.5.8-8.3 5-11" />
                  <path d="M9 9c-3 0-5-2-5-5 4 0 7 2 7 6" />
                  <path d="M14 12c0-4 3-6 7-6 0 5-3 7-7 7" /></svg
              ></span>
              <div>
                <span class="stat-label">Siswa Aktif</span><strong>28</strong
                ><small>87,5% minggu ini</small>
              </div>
            </div>
            <div class="stat-card">
              <span class="stat-icon"
                ><svg
                  aria-hidden="true"
                  width="18"
                  height="18"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  style="flex-shrink: 0"
                >
                  <path d="M12 7v14" />
                  <path
                    d="M3 18V5a2 2 0 0 1 2-2h3a4 4 0 0 1 4 4 4 4 0 0 1 4-4h3a2 2 0 0 1 2 2v13h-5a4 4 0 0 0-4 3 4 4 0 0 0-4-3H3Z"
                  /></svg
              ></span>
              <div>
                <span class="stat-label">Rata-rata Progres</span
                ><strong>68%</strong><small>Naik 12% dari minggu lalu</small>
              </div>
            </div>
            <div class="stat-card">
              <span class="stat-icon"
                ><svg
                  aria-hidden="true"
                  width="18"
                  height="18"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  style="flex-shrink: 0"
                >
                  <path d="m4 12 5 5L20 6" /></svg
              ></span>
              <div>
                <span class="stat-label">Perlu Diperiksa</span><strong>8</strong
                ><small>Pengumpulan tugas</small>
              </div>
            </div>
          </div>
          <div
            class="filter-tabs"
            style="
              width: max-content;
              margin-bottom: 24px;
              max-width: 100%;
              flex-wrap: wrap;
            "
          >
            <button
              class="btn btn-ghost selected"
              type="button"
              data-teacher-tab="0"
            >
              Monitoring Siswa</button
            ><button class="btn btn-ghost" type="button" data-teacher-tab="1">
              Manajemen Konten</button
            ><button class="btn btn-ghost" type="button" data-teacher-tab="2">
              Pengumpulan Tugas
            </button>
          </div>
          <section class="panel">
            <h2 id="teacher-tab-heading">Monitoring Siswa</h2>
            <div class="table-wrap" id="teacher-panel-0">
              <table>
                <thead>
                  <tr>
                    <th>Nama Siswa</th>
                    <th>Progres</th>
                    <th>Rata-rata Nilai</th>
                    <th>Aktivitas Terakhir</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      Aurora Artika<small style="display: block">VII A</small>
                    </td>
                    <td>
                      <div class="progress-track">
                        <div style="width: 75%"></div>
                      </div>
                      <small>75%</small>
                    </td>
                    <td>90</td>
                    <td>Menyelesaikan E-LKPD</td>
                    <td><span class="badge">Aktif</span></td>
                  </tr>
                  <tr>
                    <td>
                      Alya Putri<small style="display: block">VII A</small>
                    </td>
                    <td>
                      <div class="progress-track">
                        <div style="width: 68%"></div>
                      </div>
                      <small>68%</small>
                    </td>
                    <td>86</td>
                    <td>Mengirim pertanyaan</td>
                    <td><span class="badge">Aktif</span></td>
                  </tr>
                  <tr>
                    <td>
                      Bima Pratama<small style="display: block">VII A</small>
                    </td>
                    <td>
                      <div class="progress-track">
                        <div style="width: 61%"></div>
                      </div>
                      <small>61%</small>
                    </td>
                    <td>82</td>
                    <td>Menyelesaikan E-LKPD</td>
                    <td><span class="badge">Aktif</span></td>
                  </tr>
                  <tr>
                    <td>
                      Dika Saputra<small style="display: block">VII A</small>
                    </td>
                    <td>
                      <div class="progress-track">
                        <div style="width: 54%"></div>
                      </div>
                      <small>54%</small>
                    </td>
                    <td>78</td>
                    <td>Mengirim pertanyaan</td>
                    <td><span class="badge">Aktif</span></td>
                  </tr>
                  <tr>
                    <td>
                      Nadia Rahma<small style="display: block">VII A</small>
                    </td>
                    <td>
                      <div class="progress-track">
                        <div style="width: 47%"></div>
                      </div>
                      <small>47%</small>
                    </td>
                    <td>74</td>
                    <td>Menyelesaikan E-LKPD</td>
                    <td><span class="badge yellow">Perlu Dukungan</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="table-wrap" id="teacher-panel-1" hidden>
              <table>
                <thead>
                  <tr>
                    <th>Judul</th>
                    <th>Jenis</th>
                    <th>Status</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody id="teacher-content">
                  <tr>
                    <td>Bilangan Bulat</td>
                    <td>Materi</td>
                    <td><span class="badge">Dipublikasikan</span></td>
                    <td>
                      <button
                        class="btn btn-outline"
                        type="button"
                        data-toggle-publish
                      >
                        Jadikan Draf
                      </button>
                    </td>
                  </tr>
                  <tr>
                    <td>Mengenal Bilangan Negatif</td>
                    <td>Video</td>
                    <td><span class="badge">Dipublikasikan</span></td>
                    <td>
                      <button
                        class="btn btn-outline"
                        type="button"
                        data-toggle-publish
                      >
                        Jadikan Draf
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="table-wrap" id="teacher-panel-2" hidden>
              <table>
                <thead>
                  <tr>
                    <th>Siswa / Kelompok</th>
                    <th>Tugas</th>
                    <th>Jawaban Uraian</th>
                    <th>Status</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Kelompok 3 · Daun Cerdas</td>
                    <td>Investigasi Suhu Harian</td>
                    <td>Rentang A: 8 − (−3) = 11°C.</td>
                    <td class="grade-state">Menunggu Penilaian</td>
                    <td>
                      <button
                        type="button"
                        class="btn btn-outline"
                        data-grade-student="Kelompok 3 · Daun Cerdas"
                      >
                        Periksa & Beri Nilai
                      </button>
                    </td>
                  </tr>
                  <tr>
                    <td>Alya Putri</td>
                    <td>Investigasi Suhu Harian</td>
                    <td>Rentang A: 8 − (−3) = 11°C.</td>
                    <td class="grade-state">Menunggu Penilaian</td>
                    <td>
                      <button
                        type="button"
                        class="btn btn-outline"
                        data-grade-student="Alya Putri"
                      >
                        Periksa & Beri Nilai
                      </button>
                    </td>
                  </tr>
                  <tr>
                    <td>Bima Pratama</td>
                    <td>Investigasi Suhu Harian</td>
                    <td>Rentang A: 8 − (−3) = 11°C.</td>
                    <td class="grade-state">Menunggu Penilaian</td>
                    <td>
                      <button
                        type="button"
                        class="btn btn-outline"
                        data-grade-student="Bima Pratama"
                      >
                        Periksa & Beri Nilai
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>
          <div class="dashboard-columns" style="margin-top: 24px">
            <section class="panel">
              <h2>Aktivitas Pembelajaran Terkini</h2>
              <div class="activity-row">
                <span class="activity-icon"
                  ><svg
                    aria-hidden="true"
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    style="flex-shrink: 0"
                  >
                    <path d="M7 20h10" />
                    <path d="M10 20c5.5-2.5.8-8.3 5-11" />
                    <path d="M9 9c-3 0-5-2-5-5 4 0 7 2 7 6" />
                    <path d="M14 12c0-4 3-6 7-6 0 5-3 7-7 7" /></svg></span
                ><strong>Alya mengirim pertanyaan baru di forum</strong>
              </div>
              <div class="activity-row">
                <span class="activity-icon"
                  ><svg
                    aria-hidden="true"
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    style="flex-shrink: 0"
                  >
                    <path d="M7 20h10" />
                    <path d="M10 20c5.5-2.5.8-8.3 5-11" />
                    <path d="M9 9c-3 0-5-2-5-5 4 0 7 2 7 6" />
                    <path d="M14 12c0-4 3-6 7-6 0 5-3 7-7 7" /></svg></span
                ><strong>Kelompok 3 mengumpulkan investigasi suhu</strong>
              </div>
              <div class="activity-row">
                <span class="activity-icon"
                  ><svg
                    aria-hidden="true"
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    style="flex-shrink: 0"
                  >
                    <path d="M7 20h10" />
                    <path d="M10 20c5.5-2.5.8-8.3 5-11" />
                    <path d="M9 9c-3 0-5-2-5-5 4 0 7 2 7 6" />
                    <path d="M14 12c0-4 3-6 7-6 0 5-3 7-7 7" /></svg></span
                ><strong>24 siswa menyelesaikan video Bilangan Bulat</strong>
              </div>
            </section>
            <section class="panel">
              <h2>Perhatian untuk Siswa</h2>
              <p>
                4 siswa belum menyelesaikan aktivitas Mengamati & Menanya. Ajak
                mereka berdiskusi dan bantu menemukan kesulitan belajarnya.
              </p>
              <p style="margin-top: 20px">
                <a class="btn btn-outline" href="{{ url('/forum') }}"
                  >Buka Forum
                  <svg
                    aria-hidden="true"
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    style="flex-shrink: 0"
                  >
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path
                      d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"
                    /></svg
                ></a>
              </p>
            </section>
          </div>
          <div id="teacher-modal" class="modal-backdrop" hidden>
            <form class="modal" id="teacher-form">
              <div class="modal-head">
                <h2>Buat Aktivitas Baru (demo)</h2>
                <button type="button" class="btn btn-ghost" data-close-modal>
                  ✕
                </button>
              </div>
              <div class="field">
                <label for="activity-kind">Jenis aktivitas</label
                ><select id="activity-kind">
                  <option>Materi</option>
                  <option>Video</option>
                  <option>Tugas</option>
                  <option>Kuis</option>
                </select>
              </div>
              <div class="field">
                <label for="activity-name">Judul aktivitas</label
                ><input id="activity-name" required />
              </div>
              <div class="field">
                <label for="activity-description">Deskripsi / soal</label
                ><textarea id="activity-description" required></textarea>
              </div>
              <button type="submit" class="btn btn-primary">
                Simpan Draf (demo)
              </button>
            </form>
          </div>
          <div id="grade-modal" class="modal-backdrop" hidden>
            <form id="grade-form" class="modal">
              <div class="modal-head">
                <h2>Penilaian Tugas (demo)</h2>
                <button type="button" class="btn btn-ghost" data-close-modal>
                  ✕
                </button>
              </div>
              <p id="grade-name"></p>
              <p>Jawaban: Rentang suhu kota A 11°C dan kota B 12°C.</p>
              <div class="field">
                <label for="grade-number">Nilai (0–100)</label
                ><input
                  type="number"
                  id="grade-number"
                  min="0"
                  max="100"
                  required
                />
              </div>
              <div class="field">
                <label for="grade-feedback">Feedback</label
                ><textarea id="grade-feedback" required></textarea>
              </div>
              <button class="btn btn-primary" type="submit">
                Simpan Penilaian (demo)
              </button>
            </form>
          </div>
          <div class="inline-notice" id="teacher-notice" hidden></div>
        </main>
      </div>
    </div>
  </body>
</html>
