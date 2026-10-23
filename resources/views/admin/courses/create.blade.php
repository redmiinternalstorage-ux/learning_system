@extends('admin.layout')
@section('title', 'New Course')
@section('page-title', 'Add New Course')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('admin.courses') }}"
       class="inline-flex items-center gap-2 text-slate-400 hover:text-purple-600 dark:hover:text-purple-400 text-xs font-bold mb-5 transition">
        <i class="fa-solid fa-arrow-left text-[10px]"></i> Back to Courses
    </a>

    <div class="glass rounded-3xl p-7">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-100 dark:border-white/5">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-base shadow-lg shadow-emerald-500/25">
                <i class="fa-solid fa-circle-plus"></i>
            </div>
            <div>
                <h2 class="font-extrabold text-slate-800 dark:text-slate-100 text-sm font-outfit">Create Course</h2>
                <p class="text-slate-400 text-xs mt-0.5">Fill in the details below</p>
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

        <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Basic Info --}}
            <div>
                <p class="section-label form-section">Basic Info</p>
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Course Title *</label>
                        <input type="text" name="title" value="{{ old('title') }}" required class="inp"
                               placeholder="e.g. Complete Web Development Bootcamp">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Short Description</label>
                        <input type="text" name="short_description" value="{{ old('short_description') }}" class="inp"
                               placeholder="One-line summary shown on course cards">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Full Description</label>
                        <textarea name="description" rows="4" class="inp resize-none"
                                  placeholder="Detailed course description...">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Details Grid --}}
            <div>
                <p class="section-label form-section">Course Details</p>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Instructor *</label>
                        <input type="text" name="instructor_name" value="{{ old('instructor_name') }}" required class="inp">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Category</label>
                        <select name="category" class="inp font-bold">
                            @foreach(['Web Development','Data Science','Design','AI & ML','Mobile Development','Marketing'] as $cat)
                                <option value="{{ $cat }}" {{ old('category')==$cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Level</label>
                        <select name="level" class="inp font-bold">
                            @foreach(['Beginner','Intermediate','Advanced'] as $lv)
                                <option value="{{ $lv }}" {{ old('level')==$lv ? 'selected' : '' }}>{{ $lv }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Duration (hours) *</label>
                        <input type="number" name="duration_hours" value="{{ old('duration_hours',0) }}" min="0" required class="inp">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Lessons Count</label>
                        <input type="number" name="lessons_count" value="{{ old('lessons_count',0) }}" min="0" class="inp">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Price (₹ · 0 = Free)</label>
                        <input type="number" name="price" value="{{ old('price',0) }}" min="0" step="0.01" class="inp">
                    </div>
                </div>
            </div>

            {{-- Video --}}
            <div>
                <p class="section-label form-section">Course Video</p>
                <div class="space-y-4">
                    <div class="grid grid-cols-3 gap-3" id="video-tabs">
                        @php $oldType = old('video_type',''); @endphp
                        <button type="button" id="tab-none"   onclick="setVideoType('none')"
                                class="tab-btn {{ $oldType=='' ? 'tab-active' : 'tab-inactive' }}">
                            <i class="fa-solid fa-ban"></i> None
                        </button>
                        <button type="button" id="tab-link"   onclick="setVideoType('link')"
                                class="tab-btn {{ $oldType=='link' ? 'tab-active' : 'tab-inactive' }}">
                            <i class="fa-brands fa-youtube"></i> Link
                        </button>
                        <button type="button" id="tab-upload" onclick="setVideoType('upload')"
                                class="tab-btn {{ $oldType=='upload' ? 'tab-active' : 'tab-inactive' }}">
                            <i class="fa-solid fa-upload"></i> Upload
                        </button>
                    </div>
                    <input type="hidden" name="video_type" id="video_type_val" value="{{ $oldType }}">

                    <div id="section-link" class="{{ $oldType=='link' ? '' : 'hidden' }}">
                        <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Video URL</label>
                        <div class="relative">
                            <i class="fa-solid fa-link absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="url" name="video_link" value="{{ old('video_link') }}"
                                   placeholder="https://www.youtube.com/watch?v=..."
                                   class="inp pl-10">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1.5">Supports YouTube, Vimeo, or any direct MP4 link</p>
                    </div>

                    <div id="section-upload" class="{{ $oldType=='upload' ? '' : 'hidden' }}">
                        <div id="drop-zone"
                             class="border-2 border-dashed border-slate-200 dark:border-white/10 rounded-2xl p-8 text-center cursor-pointer hover:border-purple-400 hover:bg-purple-50/30 dark:hover:bg-purple-950/20 transition-all"
                             onclick="document.getElementById('video_file').click()"
                             ondragover="event.preventDefault();this.classList.add('border-purple-400','bg-purple-50')"
                             ondragleave="this.classList.remove('border-purple-400','bg-purple-50')"
                             ondrop="handleDrop(event)">
                            <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/30 flex items-center justify-center mx-auto mb-3">
                                <i class="fa-solid fa-cloud-arrow-up text-xl text-purple-500"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-600 dark:text-slate-300">Click or drag & drop</p>
                            <p class="text-xs text-slate-400 mt-1">MP4, MOV, AVI, MKV, WEBM · max 500MB</p>
                            <p id="file-name" class="text-xs text-emerald-600 font-bold mt-2 hidden"></p>
                        </div>
                        <input type="file" name="video_file" id="video_file" accept="video/*" class="hidden" onchange="showFileName(this)">
                    </div>
                </div>
            </div>

            {{-- Publish toggle --}}
            <label class="flex items-center gap-3 p-4 rounded-2xl border border-slate-200 dark:border-white/8 cursor-pointer hover:bg-slate-50 dark:hover:bg-white/[0.02] transition group">
                <div class="relative">
                    <input type="checkbox" name="is_published" id="is_published" value="1"
                           {{ old('is_published') ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 rounded-full peer-checked:bg-gradient-to-r peer-checked:from-emerald-500 peer-checked:to-teal-600 transition-all"></div>
                    <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-all peer-checked:translate-x-5"></div>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-200">Publish immediately</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">Make this course visible to students right away</p>
                </div>
            </label>

            <div class="flex gap-3 pt-1">
                <button type="submit" class="btn-primary flex-1">
                    <i class="fa-solid fa-circle-plus"></i> Create Course
                </button>
                <a href="{{ route('admin.courses') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    .tab-btn  { display:flex; align-items:center; justify-content:center; gap:0.5rem; padding:0.65rem; border-radius:0.75rem; font-size:0.75rem; font-weight:700; cursor:pointer; transition:all 0.2s; border:2px solid; }
    .tab-active   { border-color:#d946ef; background:#fdf4ff; color:#a21caf; }
    .dark .tab-active { background:rgba(162,28,175,0.2); color:#e879f9; border-color:rgba(217,70,239,0.5); }
    .tab-inactive { border-color:rgba(203,213,225,0.8); background:transparent; color:#94a3b8; }
    .dark .tab-inactive { border-color:rgba(255,255,255,0.08); }
    .tab-inactive:hover { border-color:#d946ef; color:#a21caf; }
    .dark .tab-inactive:hover { color:#e879f9; border-color:rgba(217,70,239,0.3); }
</style>
@endpush

@push('scripts')
<script>
function setVideoType(type) {
    ['none','link','upload'].forEach(t => {
        document.getElementById('tab-'+t).className = 'tab-btn tab-inactive';
    });
    document.getElementById('tab-'+type).className = 'tab-btn tab-active';
    document.getElementById('video_type_val').value = (type === 'none' ? '' : type);
    document.getElementById('section-link').classList.add('hidden');
    document.getElementById('section-upload').classList.add('hidden');
    if (type === 'link')   document.getElementById('section-link').classList.remove('hidden');
    if (type === 'upload') document.getElementById('section-upload').classList.remove('hidden');
}
function showFileName(input) {
    if (input.files && input.files[0]) {
        const el = document.getElementById('file-name');
        el.textContent = '✓ ' + input.files[0].name;
        el.classList.remove('hidden');
    }
}
function handleDrop(e) {
    e.preventDefault();
    document.getElementById('drop-zone').classList.remove('border-purple-400','bg-purple-50');
    const files = e.dataTransfer.files;
    if (files.length) { document.getElementById('video_file').files = files; showFileName(document.getElementById('video_file')); }
}
</script>
@endpush
@endsection
