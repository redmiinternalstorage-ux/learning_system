@php
    $currentAd = \Illuminate\Support\Facades\Cache::remember('active_ad_overlay', 120, function () {
        return \App\Models\Ad::active()->latest()->first();
    });
@endphp

@if($currentAd)
<div id="ad-overlay" class="fixed inset-0 z-[9999] flex items-center justify-center bg-emerald-950/40 backdrop-blur-md transition-opacity duration-300 hidden">
    <div class="relative w-full max-w-4xl mx-4 bg-white border border-emerald-100 rounded-3xl overflow-hidden shadow-2xl flex flex-col md:flex-row max-h-[88vh]">
        
        <!-- Media Area -->
        <div class="relative flex-1 bg-black flex items-center justify-center min-h-[40vh] md:min-h-[50vh]">
            @if($currentAd->type === 'video')
                <video id="ad-media-player" src="{{ $currentAd->media_path }}" autoplay muted playsinline loop class="w-full h-full object-contain max-h-[60vh] md:max-h-[80vh]"></video>
                
                <!-- Unmute Toggle Overlay -->
                <button id="ad-audio-toggle" onclick="toggleAdAudio()" class="absolute bottom-4 left-4 z-20 w-10 h-10 rounded-full bg-black/60 border border-white/10 text-white flex items-center justify-center hover:bg-black/80 transition shadow-md">
                    <i id="ad-audio-icon" class="fa-solid fa-volume-xmark text-sm"></i>
                </button>
            @else
                @if($currentAd->link_url)
                    <a href="{{ $currentAd->link_url }}" target="_blank" class="w-full h-full flex items-center justify-center">
                        <img src="{{ $currentAd->media_path }}" alt="{{ $currentAd->title }}" class="w-full h-full object-contain max-h-[60vh] md:max-h-[80vh]">
                    </a>
                @else
                    <img src="{{ $currentAd->media_path }}" alt="{{ $currentAd->title }}" class="w-full h-full object-contain max-h-[60vh] md:max-h-[80vh]">
                @endif
            @endif

            <!-- Brand Overlay Badge -->
            <div class="absolute top-4 left-4 z-[9999] bg-white/90 border border-emerald-100 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest text-emerald-700 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Sponsored
            </div>
        </div>

        <div class="w-full md:w-80 bg-emerald-50 p-6 flex flex-col justify-between border-t md:border-t-0 md:border-l border-emerald-100">
            <div class="space-y-4">
                <div>
                    <span class="text-[9px] font-extrabold uppercase tracking-widest text-emerald-600">Announcement</span>
                    <h4 class="text-slate-900 font-extrabold text-lg font-outfit mt-1 tracking-tight leading-tight">{{ $currentAd->title }}</h4>
                    <p class="text-slate-500 text-[11px] mt-1">Powered by <span class="text-emerald-700 font-bold">{{ $currentAd->powered_by }}</span></p>
                </div>

                @if($currentAd->description)
                <div class="p-3.5 rounded-2xl bg-white border border-emerald-100 text-slate-600 text-xs leading-relaxed max-h-36 overflow-y-auto">
                    {{ $currentAd->description }}
                </div>
                @endif

                @if($currentAd->link_url)
                <div>
                    <a href="{{ $currentAd->link_url }}" target="_blank" class="inline-flex items-center gap-2 text-xs font-bold text-emerald-400 hover:text-emerald-300 transition-colors uppercase tracking-wider font-outfit">
                        Visit Website <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                    </a>
                </div>
                @endif
            </div>

            <!-- Action button area -->
            <div class="mt-6 space-y-3">
                <!-- Skip Progress Indicator -->
                <div id="ad-skip-timer-container" class="text-slate-500 text-[10px] font-bold uppercase tracking-wider flex items-center gap-2">
                    <div class="w-4 h-4 border-2 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
                    <span>Skip in <span id="ad-countdown" class="text-emerald-800 font-black">5</span>s</span>
                </div>

                <button id="ad-skip-btn" onclick="closeAdCampaign()" disabled
                        class="w-full py-3 rounded-2xl text-xs font-extrabold uppercase tracking-wider text-slate-400 bg-white border border-emerald-100 cursor-not-allowed transition duration-200 flex items-center justify-center gap-2 font-outfit">
                    <i class="fa-solid fa-circle-xmark"></i> Close
                </button>
            </div>
        </div>
        
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const overlay = document.getElementById('ad-overlay');
        const countdownText = document.getElementById('ad-countdown');
        const skipBtn = document.getElementById('ad-skip-btn');
        const timerContainer = document.getElementById('ad-skip-timer-container');
        
        if (overlay && !sessionStorage.getItem('lc_ad_seen')) {
            overlay.classList.remove('hidden');
            
            let count = 5;
            const interval = setInterval(function() {
                count--;
                if (countdownText) countdownText.textContent = count;
                
                if (count <= 0) {
                    clearInterval(interval);
                    if (timerContainer) timerContainer.classList.add('hidden');
                    if (skipBtn) {
                        skipBtn.removeAttribute('disabled');
                        skipBtn.classList.remove('text-slate-400', 'bg-white', 'border-emerald-100', 'cursor-not-allowed');
                        skipBtn.classList.add('text-white', 'bg-emerald-600', 'hover:bg-emerald-700', 'border-transparent', 'shadow-lg', 'shadow-emerald-600/20');
                    }
                }
            }, 1000);
        }
    });

    function closeAdCampaign() {
        const overlay = document.getElementById('ad-overlay');
        if (overlay) overlay.classList.add('hidden');
        try { sessionStorage.setItem('lc_ad_seen', '1'); } catch (e) {}
        
        const mediaPlayer = document.getElementById('ad-media-player');
        if (mediaPlayer) {
            mediaPlayer.pause();
        }
    }

    function toggleAdAudio() {
        const player = document.getElementById('ad-media-player');
        const icon = document.getElementById('ad-audio-icon');
        if (player) {
            player.muted = !player.muted;
            if (player.muted) {
                icon.className = 'fa-solid fa-volume-xmark text-sm';
            } else {
                icon.className = 'fa-solid fa-volume-high text-sm';
            }
        }
    }
</script>
@endif
