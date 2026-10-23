{{--
    Global Loading Overlay — Learning Curve
    Include once in every layout just before </body>
    Usage: @include('partials.global-loader')
--}}

{{-- Top slim progress bar --}}
<div id="loader-topbar"></div>

{{-- Full-screen overlay --}}
<div id="global-loader" role="status" aria-live="polite" aria-label="Loading">

    <div class="loader-card">

        {{-- Wi-Fi signal icon --}}
        <div class="wifi-icon">
            <div class="wifi-arc wifi-arc-1"></div>
            <div class="wifi-arc wifi-arc-2"></div>
            <div class="wifi-arc wifi-arc-3"></div>
            <div class="wifi-arc wifi-arc-r-1"></div>
            <div class="wifi-arc wifi-arc-r-2"></div>
            <div class="wifi-arc wifi-arc-r-3"></div>
            <div class="wifi-dot"></div>
        </div>

        {{-- Signal bars --}}
        <div class="signal-bars">
            <div class="signal-bar"></div>
            <div class="signal-bar"></div>
            <div class="signal-bar"></div>
            <div class="signal-bar"></div>
            <div class="signal-bar"></div>
        </div>

        {{-- Text --}}
        <div class="loader-text-wrap">
            <div class="loader-title">
                <span id="loader-msg-title">Loading</span>
                <span class="loader-dots">
                    <span></span><span></span><span></span>
                </span>
            </div>
            <div class="loader-subtitle" id="loader-msg-sub">Please wait...</div>
        </div>

        {{-- Progress bar --}}
        <div class="loader-progress-track">
            <div class="loader-progress-bar" id="loader-progress-bar"></div>
        </div>

    </div>

</div>

{{-- Load loader assets --}}
<link rel="stylesheet" href="/css/loader.css">
<script src="/js/loader.js"></script>
