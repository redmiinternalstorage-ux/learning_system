@extends('admin.layout')
@section('title', 'Edit Ad Campaign')
@section('page-title', 'Edit Ad Campaign')

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
                <i class="fa-solid fa-pen-to-square"></i>
            </div>
            <div>
                <h2 class="font-extrabold text-slate-800 dark:text-slate-100 text-sm font-outfit">Edit Campaign</h2>
                <p class="text-slate-400 text-xs mt-0.5 truncate max-w-xs">{{ $ad->title }}</p>
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

        <form method="POST" action="{{ route('admin.ads.update', $ad) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf @method('PUT')

            {{-- Title, Sponsor & Description --}}
            <div>
                <p class="section-label form-section mb-3">1. Campaign Basic Info</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Campaign Title *</label>
                        <input type="text" name="title" value="{{ old('title', $ad->title) }}" required class="inp">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Powered By / Sponsor *</label>
                        <input type="text" name="powered_by" value="{{ old('powered_by', $ad->powered_by) }}" required class="inp">
                    </div>
                </div>

                <div class="space-y-1.5 mt-4">
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Ad Description / Details</label>
                    <textarea name="description" rows="3" class="inp" placeholder="Describe advertisement details...">{{ old('description', $ad->description) }}</textarea>
                </div>
            </div>

            {{-- Ad Format --}}
            <div>
                <p class="section-label form-section mb-3">2. Ad Media Format</p>
                <div class="grid grid-cols-2 gap-3">
                    <label id="lbl-image"
                           class="ad-type-btn {{ $ad->type === 'image' ? 'ad-type-active' : 'ad-type-inactive' }}"
                           onclick="setAdType('image')">
                        <input type="radio" name="type" value="image" class="hidden" {{ $ad->type === 'image' ? 'checked' : '' }}>
                        <i class="fa-solid fa-image text-xl mb-1"></i>
                        <span class="font-bold text-xs">Image Banner</span>
                        <span class="text-[10px] opacity-70">Static banner</span>
                    </label>
                    <label id="lbl-video"
                           class="ad-type-btn {{ $ad->type === 'video' ? 'ad-type-active' : 'ad-type-inactive' }}"
                           onclick="setAdType('video')">
                        <input type="radio" name="type" value="video" class="hidden" {{ $ad->type === 'video' ? 'checked' : '' }}>
                        <i class="fa-solid fa-circle-play text-xl mb-1"></i>
                        <span class="font-bold text-xs">Video Ad</span>
                        <span class="text-[10px] opacity-70">Autoplay video</span>
                    </label>
                </div>
            </div>

            {{-- Current Media Preview --}}
            <div>
                <p class="section-label form-section mb-3">3. Current Media</p>
                <div class="rounded-2xl overflow-hidden border border-slate-200 dark:border-white/8 bg-slate-50 dark:bg-white/[0.02] p-3 flex items-center justify-center max-h-52">
                    @if($ad->type === 'image')
                        <img src="{{ $ad->media_path }}" alt="Preview" class="max-h-44 w-auto rounded-xl object-contain">
                    @else
                        <video src="{{ $ad->media_path }}" controls class="max-h-44 w-auto rounded-xl"></video>
                    @endif
                </div>
                <p class="text-[10px] text-slate-400 mt-1.5 truncate">{{ $ad->media_path }}</p>
            </div>

            {{-- Update Media --}}
            <div>
                <p class="section-label form-section mb-3">4. Replace Media File / URL (Optional)</p>
                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Upload New File</label>
                        <input type="file" name="media_file"
                               class="inp file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:uppercase file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                        <p class="text-[10px] text-slate-400">Leave empty to keep current file. JPG, PNG, GIF, MP4 — max 25MB</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="flex-1 h-px bg-slate-200 dark:bg-white/10"></div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">or</span>
                        <div class="flex-1 h-px bg-slate-200 dark:bg-white/10"></div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">New External URL</label>
                        <input type="text" name="media_url"
                               value="{{ old('media_url', str_starts_with($ad->media_path, '/uploads/') ? '' : $ad->media_path) }}"
                               class="inp" placeholder="https://example.com/banner.jpg">
                    </div>
                </div>
            </div>

            {{-- Schedule & Links --}}
            <div>
                <p class="section-label form-section mb-3">5. Schedule &amp; Links</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Start Date</label>
                        <input type="date" name="start_date" value="{{ old('start_date', $ad->start_date ? $ad->start_date->format('Y-m-d') : '') }}" class="inp">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Duration (Days)</label>
                        <input type="number" name="duration_days" value="{{ old('duration_days', $ad->duration_days ?? 7) }}" min="1" class="inp">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Click Destination URL</label>
                        <input type="url" name="link_url" value="{{ old('link_url', $ad->link_url) }}" class="inp" placeholder="https://">
                    </div>
                </div>
            </div>

            {{-- Financial Collection Tracking --}}
            <div>
                <p class="section-label form-section mb-3">6. Financial Collection Tracking</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Collection Amount (₹)</label>
                        <input type="number" step="0.01" name="total_amount" value="{{ old('total_amount', $ad->total_amount) }}" class="inp">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Advance Payment Received (₹)</label>
                        <input type="number" step="0.01" name="advance_paid" value="{{ old('advance_paid', $ad->advance_paid) }}" class="inp">
                    </div>
                </div>
            </div>

            {{-- Publish toggle --}}
            <label class="flex items-center gap-3 p-4 rounded-2xl border border-slate-200 dark:border-white/8 cursor-pointer hover:bg-slate-50 dark:hover:bg-white/[0.02] transition">
                <div class="relative">
                    <input type="checkbox" name="is_active" value="1"
                           {{ $ad->is_active ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 rounded-full peer-checked:bg-gradient-to-r peer-checked:from-emerald-500 peer-checked:to-teal-600 transition-all"></div>
                    <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-all peer-checked:translate-x-5"></div>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-200">Published &amp; Active</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">Ad overlay is active for platform visitors</p>
                </div>
            </label>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1">
                    <i class="fa-solid fa-floppy-disk"></i> Save Changes
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
