@props(['item'])
<article class="material-card">
    <a href="{{ url('/video') }}?materi={{ $item['id'] }}" class="material-art tone-{{ $item['tone'] }}">
        <span class="art-leaf"><x-icon name="leaf" /></span>
        <span class="math-symbol">{{ $item['symbol'] }}</span>
        <span class="art-squares">□</span>
        <span class="material-label">{{ $item['category'] }}</span>
    </a>
    <div class="material-body">
        <div class="material-meta">
            <span><x-icon name="video" :size="14" /> {{ $item['lessons'] }} video</span>
            <span><x-icon name="clock-3" :size="14" /> {{ $item['minutes'] }} menit</span>
        </div>
        <h3>{{ $item['title'] }}</h3>
        <p>{{ $item['description'] }}</p>
        <div class="material-bottom">
            <span>Kelas VII SMP</span>
            <a href="{{ url('/video') }}?materi={{ $item['id'] }}">Mulai Belajar <x-icon name="arrow-up-right" :size="17" /></a>
        </div>
    </div>
</article>
