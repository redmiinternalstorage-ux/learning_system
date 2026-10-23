@extends('admin.layout')
@section('title', 'New Ad Campaign')
@section('page-title', 'Create Ad Campaign')

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('admin.ads.index') }}"
       class="inline-flex items-center gap-2 text-slate-400 hover:text-emerald-600 text-xs font-bold mb-5 transition">
        <i class="fa-solid fa-arrow-left text-[10px]"></i> Back to Ad Manager
    </a>

    <div class="glass rounded-3xl p-7">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-100 dark:border-white/5">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center text-white text-base shadow-lg shadow-rose-500/25">
                <i class="fa-solid fa-rectangle-ad"></i>
            </div>
            <div>
                <h2 class="font-extrabold text-slate-800 dark:text-slate-100 text-sm font-outfit">New Ad Campaign</h2>
                <p class="text-slate-400 text-xs mt-0.5">Configure campaign details, media, duration &amp; financial collection</p>
            </div>
        </div>

        @if($errors->any())
        <div class="mb-5 p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900/30 space-y-1.5 anim-slide">
            @foreach($errors->all() as $e)
                <p class="text-rose-600 dark:text-rose-400 text-xs font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ $e }}
                </p>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('admin.ads.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Title, Sponsor & Description --}}
            <div>
                <p class="section-label form-section mb-3">1. Campaign Basic Info</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Campaign Title *</label>
                        <input type="text" name="title" value="{{ old('title') }}" required class="inp"
                               placeholder="e.g. Summer LMS Special Deal">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Powered By / Sponsor *</label>
                        <input type="text" name="powered_by" value="{{ old('powered_by') }}" required class="inp"
                               placeholder="e.g. TechCorp Academy">
                    </div>
                </div>

                <div class="space-y-1.5 mt-4">
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Ad Description / Details</label>
                    <textarea name="description" rows="3" class="inp" placeholder="Briefly describe the advertisement offer or details...">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- Ad Format (Image or Video) --}}
            <div>
                <p class="section-label form-section mb-3">2. Ad Media Format</p>
                <div class="grid grid-cols-2 gap-3">
                    <label id="lbl-image"
                           class="ad-type-btn ad-type-active"
                           onclick="setAdType('image')">
                        <input type="radio" name="type" value="image" class="hidden" checked>
                        <i class="fa-solid fa-image text-xl mb-1"></i>
                        <span class="font-bold text-xs">Image Banner</span>
                        <span class="text-[10px] opacity-70">JPG, PNG, GIF graphics</span>
                    </label>
                    <label id="lbl-video"
                           class="ad-type-btn ad-type-inactive"
                           onclick="setAdType('video')">
                        <input type="radio" name="type" value="video" class="hidden">
                        <i class="fa-solid fa-circle-play text-xl mb-1"></i>
                        <span class="font-bold text-xs">Video Ad</span>
                        <span class="text-[10px] opacity-70">Autoplay MP4 video</span>
                    </label>
                </div>
            </div>

            {{-- Media Source --}}
            <div>
                <p class="section-label form-section mb-3">3. Media File / URL</p>
                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Upload File (Image or Video)</label>
                        <input type="file" name="media_file"
                               class="inp file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:uppercase file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                        <p class="text-[10px] text-slate-400">Supported: JPG, PNG, GIF, MP4 — max 25MB</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="flex-1 h-px bg-slate-200 dark:bg-white/10"></div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">or</span>
                        <div class="flex-1 h-px bg-slate-200 dark:bg-white/10"></div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">External Direct Media URL</label>
                        <input type="text" name="media_url" value="{{ old('media_url') }}" class="inp"
                               placeholder="https://example.com/ad-banner.jpg">
                    </div>
                </div>
            </div>

            {{-- Duration & Dates --}}
            <div>
                <p class="section-label form-section mb-3">4. Schedule &amp; Duration</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Start Date</label>
                        <input type="date" name="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}" class="inp">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Duration (Days) *</label>
                        <input type="number" name="duration_days" value="{{ old('duration_days', 7) }}" min="1" required class="inp" placeholder="e.g. 7, 15, 30">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Click Redirect URL</label>
                        <input type="url" name="link_url" value="{{ old('link_url') }}" class="inp" placeholder="https://">
                    </div>
                </div>
            </div>

            {{-- Financial Collection Tracking --}}
            <div>
                <p class="section-label form-section mb-3">5. Financial Collection Tracking</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Collection Amount (₹)</label>
                        <input type="number" step="0.01" name="total_amount" value="{{ old('total_amount', 0) }}" class="inp" placeholder="0.00">
                        <p class="text-[10px] text-slate-400">Total revenue charged for this ad campaign</p>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Advance Payment Received (₹)</label>
                        <input type="number" step="0.01" name="advance_paid" value="{{ old('advance_paid', 0) }}" class="inp" placeholder="0.00">
                        <p class="text-[10px] text-slate-400">Advance amount paid by advertiser</p>
                    </div>
                </div>
            </div>

            {{-- Publish toggle --}}
            <label class="flex items-center gap-3 p-4 rounded-2xl border border-slate-200 dark:border-white/8 cursor-pointer hover:bg-slate-50 dark:hover:bg-white/[0.02] transition">
                <div class="relative">
                    <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 rounded-full peer-checked:bg-gradient-to-r peer-checked:from-emerald-500 peer-checked:to-teal-600 transition-all"></div>
                    <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-all peer-checked:translate-x-5"></div>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-200">Publish Ad Campaign Immediately</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">Ad overlay will be live on initial web visits</p>
                </div>
            </label>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1">
                    <i class="fa-solid fa-rocket"></i> Deploy Ad Campaign
                </button>
                <a href="{{ route('admin.ads.index') }}" class="btn-secondary">Discard</a>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    .ad-type-btn  { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:1rem; border-radius:1rem; border:2px solid; cursor:pointer; transition:all 0.2s; }
    .ad-type-active   { border-color:#059669; background:rgba(5,150,105,0.06); color:#047857; }
    .ad-type-inactive { border-color:rgba(203,213,225,0.8); background:transparent; color:#94a3b8; }
    .ad-type-inactive:hover { border-color:#059669; color:#047857; }
</style>
@endpush

@push('scripts')
<script>
function setAdType(type) {
    document.getElementById('lbl-image').className = 'ad-type-btn ' + (type==='image' ? 'ad-type-active' : 'ad-type-inactive');
    document.getElementById('lbl-video').className = 'ad-type-btn ' + (type==='video' ? 'ad-type-active' : 'ad-type-inactive');
    document.getElementById('lbl-' + type).querySelector('input').checked = true;
}
</script>
@endpush
@endsection
