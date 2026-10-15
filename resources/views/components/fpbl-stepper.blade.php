@php($steps = config('mathleaf.steps', []))
<div class="fpbl-component">
    <div class="fpbl-steps" data-fpbl>
        @foreach ($steps as $index => $step)
            <button type="button"
                class="fpbl-step {{ $index === 0 ? 'active' : '' }}"
                data-step-description="{{ $step['text'] }}"
                data-step-url="{{ url($step['to']) }}"
                aria-pressed="{{ $index === 0 ? 'true' : 'false' }}">
                <span class="step-icon"><x-icon :name="$step['icon']" :size="23" /></span>
                <span class="step-no">TAHAP 0{{ $index + 1 }}</span>
                <strong>{{ $step['title'] }}</strong>
                <p>{{ $step['text'] }}</p>
                @if (!$loop->last)<x-icon name="arrow-right" class="step-arrow" />@endif
            </button>
        @endforeach
    </div>
    @if (count($steps))
        <div class="step-detail">
            <span><x-icon name="check" :size="17" /> <span data-fpbl-text>{{ $steps[0]['text'] }}</span></span>
            <a class="btn btn-link" href="{{ url($steps[0]['to']) }}" data-fpbl-link>Jelajahi tahap ini <x-icon name="arrow-right" :size="16" /></a>
        </div>
    @endif
</div>
