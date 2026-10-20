{{--
    Illustrated scene for one "how it works" step (decorative; the step text carries the meaning).
    Everything animates from its resting state once the scene gets the .is-on class.
    $n: step number 1–5
--}}
@php
    $check = '<svg viewBox="0 0 16 16" aria-hidden="true"><path d="M4 8.4l2.4 2.4L12 5.2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    $pin = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22s7-6.2 7-12a7 7 0 1 0-14 0c0 5.8 7 12 7 12z" fill="currentColor"/><circle cx="12" cy="10" r="2.6" fill="#fff"/></svg>';
@endphp
<div class="wo-scene wo-scene--{{ $n }}" aria-hidden="true" @isset($shot) data-how-shot="{{ $shot }}" @endisset>
    <div class="sc">
        @switch($n)
            @case(1)
                <div class="sc-card sc1-phone">
                    <div class="sc1-screen">
                        <span class="sc-m">08:02</span>
                        <span class="sc-h">{{ __('marketing.scene_greeting') }}</span>
                        <span class="sc-bar" style="width:55%"></span>
                        <span class="sc1-btn">
                            <span class="sc1-btn__off">{{ __('marketing.scene_clock_in') }}</span>
                            <span class="sc1-btn__on">{!! $check !!}{{ __('marketing.scene_at_work') }}</span>
                        </span>
                        <span class="sc-chip sc-chip--ok sc1-status">{{ __('marketing.scene_checked_in') }}</span>
                    </div>
                </div>
                <div class="sc-card sc-map sc1-map">
                    <svg class="sc-roads" viewBox="0 0 100 70" preserveAspectRatio="none"><path d="M-5 52 C20 44 35 58 60 40 S90 18 105 22"/><path d="M30 -5 C34 20 28 40 40 75"/></svg>
                    <span class="sc1-ripple"></span>
                    <span class="sc-pin sc1-pin">{!! $pin !!}</span>
                    <span class="sc-chip sc1-loc">{!! $pin !!}{{ __('marketing.scene_location') }}</span>
                </div>
                @break

            @case(2)
                <div class="sc-card sc2-tasks">
                    <span class="sc-h">{{ __('marketing.scene_tasks') }}</span>
                    @foreach ([[62, true], [78, true], [48, true], [70, false]] as $k => [$w, $done])
                        <div class="sc2-row" style="--d: {{ 0.3 + $k * 0.35 }}s">
                            <span class="sc-check{{ $done ? ' sc-check--will' : '' }}">{!! $check !!}</span>
                            <span class="sc-bar" style="width: {{ $w }}%"></span>
                        </div>
                    @endforeach
                </div>
                <div class="sc-card sc2-report">
                    <div class="sc-head">
                        <span class="sc-h">{{ __('marketing.scene_report') }}</span>
                        <span class="sc-swap">
                            <span class="sc-chip">{{ __('marketing.scene_sending') }}</span>
                            <span class="sc-chip sc-chip--ok">{!! $check !!}{{ __('marketing.scene_sent') }}</span>
                        </span>
                    </div>
                    <div class="sc2-photos">
                        @foreach ([0, 1, 2] as $k)
                            <span class="sc2-photo" style="--d: {{ 0.6 + $k * 0.2 }}s"><svg viewBox="0 0 24 24"><path d="M3 18l5-6 4 4 3-3 6 5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><circle cx="16" cy="8" r="2" fill="currentColor"/></svg></span>
                        @endforeach
                    </div>
                    <span class="sc-progress"><span></span></span>
                </div>
                @break

            @case(3)
                <div class="sc-card sc-map sc3-map">
                    <svg class="sc-roads" viewBox="0 0 100 70" preserveAspectRatio="none"><path d="M-5 30 C25 22 40 40 70 30 S95 10 105 14"/><path d="M55 -5 C50 25 62 45 52 75"/><path d="M-5 62 C30 56 60 66 105 52"/></svg>
                    @foreach ([[22, 34, 'ok'], [48, 22, 'ok'], [63, 55, 'ok'], [80, 30, 'ok'], [34, 66, 'off']] as $k => [$x, $y, $state])
                        <span class="sc-pin sc3-pin sc3-pin--{{ $state }}" style="left: {{ $x }}%; top: {{ $y }}%; --d: {{ 0.3 + $k * 0.25 }}s">{!! $pin !!}</span>
                    @endforeach
                </div>
                <div class="sc-card sc3-list">
                    <div class="sc-head">
                        <span class="sc-h">{{ __('marketing.scene_at_work') }}</span>
                        <span class="sc3-count">4</span>
                    </div>
                    @foreach ([['AB', '#1f66e5', 70], ['EK', '#0e9f6e', 55], ['DS', '#7c5cff', 64], ['LM', '#e08a00', 48]] as $k => [$ini, $color, $w])
                        <div class="sc3-row" style="--d: {{ 0.5 + $k * 0.25 }}s">
                            <span class="sc-avatar" style="background: {{ $color }}">{{ $ini }}</span>
                            <span class="sc-bar" style="width: {{ $w }}%"></span>
                            <span class="sc-dot"></span>
                        </div>
                    @endforeach
                </div>
                @break

            @case(4)
                <div class="sc-card sc4-week">
                    <div class="sc-head">
                        <span class="sc-h">{{ __('marketing.scene_week') }}</span>
                        <span class="sc4-total">41h 30m</span>
                    </div>
                    <div class="sc4-bars">
                        @foreach (explode(',', __('marketing.scene_days')) as $k => $day)
                            @php [$h, $o] = [[78, 0], [80, 14], [76, 0], [80, 22], [70, 0]][$k] ?? [70, 0]; @endphp
                            <div class="sc4-col" style="--d: {{ 0.2 + $k * 0.15 }}s">
                                <span class="sc4-bar" style="--h: {{ $h + $o }}%">
                                    @if ($o)<span class="sc4-over" style="--o: {{ round($o / ($h + $o) * 100) }}%"></span>@endif
                                </span>
                                <span class="sc-m">{{ $day }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="sc-card sc4-pay">
                    <span class="sc-h">{{ __('marketing.scene_payroll') }}</span>
                    <div class="sc4-line" style="--d: 1.2s"><span class="sc-m">{{ __('marketing.scene_regular') }}</span><b>40h</b></div>
                    <div class="sc4-line" style="--d: 1.45s"><span class="sc-m">{{ __('marketing.scene_overtime') }}</span><b class="sc4-ot">1h 30m</b></div>
                    <span class="sc-chip sc-chip--ok sc4-ready">{!! $check !!}{{ __('marketing.scene_ready') }}</span>
                </div>
                @break

            @case(5)
                <div class="sc-card sc5-offer">
                    <div class="sc-head">
                        <span class="sc-h">{{ __('marketing.scene_offer') }} #128</span>
                        <span class="sc-swap">
                            <span class="sc-chip">{{ __('marketing.scene_sent') }}</span>
                            <span class="sc-chip sc-chip--ok">{!! $check !!}{{ __('marketing.scene_accepted') }}</span>
                        </span>
                    </div>
                    <span class="sc-bar" style="width: 82%"></span>
                    <span class="sc-bar" style="width: 64%"></span>
                    <span class="sc-bar" style="width: 72%"></span>
                    <span class="sc5-amount"></span>
                </div>
                <div class="sc-card sc5-chart">
                    <span class="sc-h">{{ __('marketing.scene_finance') }}</span>
                    <div class="sc5-legend">
                        <span><i class="sc5-in"></i>{{ __('marketing.scene_income') }}</span>
                        <span><i class="sc5-out"></i>{{ __('marketing.scene_expenses') }}</span>
                    </div>
                    <div class="sc5-bars">
                        @foreach ([[48, 34], [56, 38], [52, 41], [66, 40], [74, 46], [86, 50]] as $k => [$in, $out])
                            <div class="sc5-pair" style="--d: {{ 0.3 + $k * 0.12 }}s">
                                <span class="sc5-in" style="--h: {{ $in }}%"></span>
                                <span class="sc5-out" style="--h: {{ $out }}%"></span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @break
        @endswitch
    </div>
</div>
