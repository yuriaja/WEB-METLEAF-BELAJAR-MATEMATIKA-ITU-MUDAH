@props(['name' => 'leaf', 'size' => 20, 'class' => ''])
<svg xmlns="http://www.w3.org/2000/svg" width="{{ (int) $size }}" height="{{ (int) $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}" aria-hidden="true">
@switch($name)
    @case('leaf')
        <path d="M11 20A7 7 0 0 1 4 13C4 5 14 3 21 3c0 7-2 17-10 17Z"/><path d="M4 21c0-6 5-11 11-14"/>
        @break
    @case('sprout')
        <path d="M7 20h10"/><path d="M10 20c5.5-2.5.8-8.3 5-11"/><path d="M9 9c-3 0-5-2-5-5 4 0 7 2 7 6"/><path d="M14 12c0-4 3-6 7-6 0 5-3 7-7 7"/>
        @break
    @case('arrow-up-right')
        <path d="M7 17 17 7"/><path d="M7 7h10v10"/>
        @break
    @case('arrow-right')
        <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
        @break
    @case('play')
        <polygon points="6 3 20 12 6 21 6 3"/>
        @break
    @case('book-open')
        <path d="M12 7v14"/><path d="M3 18V5a2 2 0 0 1 2-2h3a4 4 0 0 1 4 4 4 4 0 0 1 4-4h3a2 2 0 0 1 2 2v13h-5a4 4 0 0 0-4 3 4 4 0 0 0-4-3H3Z"/>
        @break
    @case('sparkles')
        <path d="m12 3 1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3Z"/><path d="m5 17 .8 2.2L8 20l-2.2.8L5 23l-.8-2.2L2 20l2.2-.8L5 17Z"/><path d="m19 2 .7 1.3L21 4l-1.3.7L19 6l-.7-1.3L17 4l1.3-.7L19 2Z"/>
        @break
    @case('users')
        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
        @break
    @case('graduation-cap')
        <path d="m2 10 10-5 10 5-10 5-10-5Z"/><path d="M6 12v5c3 2 9 2 12 0v-5M22 10v7"/>
        @break
    @case('video')
        <rect x="2" y="5" width="15" height="14" rx="2"/><path d="m17 10 5-3v10l-5-3"/>
        @break
    @case('clock-3')
        <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>
        @break
    @case('check')
        <path d="m4 12 5 5L20 6"/>
        @break
    @case('award')
        <circle cx="12" cy="8" r="6"/><path d="m8 13-2 8 6-3 6 3-2-8"/>
        @break
    @case('menu')
        <path d="M4 6h16M4 12h16M4 18h16"/>
        @break
    @case('x')
        <path d="M18 6 6 18M6 6l12 12"/>
        @break
    @default
        <circle cx="12" cy="12" r="9" />
@endswitch
</svg>
