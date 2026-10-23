@extends('admin.layout')
@section('title','Site Settings')
@section('page-title','Site Settings')

@section('content')
<div class="space-y-6">

  {{-- Hero --}}
  <div class="relative rounded-3xl overflow-hidden p-7"
       style="background:linear-gradient(135deg,#1a1008 0%,#3d2800 40%,#0d0800 100%)">
    <div class="absolute inset-0 pointer-events-none">
      <div class="absolute top-0 right-0 w-72 h-72 bg-amber-500/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
    </div>
    <div class="relative z-10">
      <p class="text-amber-300 text-[10px] font-black uppercase tracking-widest mb-2">Admin Panel</p>
      <h2 class="text-white text-2xl font-black font-outfit">⚙️ Site Settings</h2>
      <p class="text-amber-200/60 text-sm mt-1.5">Configure platform behaviour and appearance</p>
    </div>
  </div>

  <form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    @method('POST')

    @php
      $groupIcons = [
        'general'    => ['icon'=>'fa-globe',        'color'=>'from-blue-500 to-cyan-600',    'label'=>'General'],
        'messages'   => ['icon'=>'fa-comment-dots', 'color'=>'from-emerald-500 to-teal-600', 'label'=>'Messages'],
        'appearance' => ['icon'=>'fa-palette',      'color'=>'from-pink-500 to-rose-600',    'label'=>'Appearance'],
        'security'   => ['icon'=>'fa-shield-halved','color'=>'from-red-500 to-rose-600',     'label'=>'Security'],
      ];
    @endphp

    <div class="grid lg:grid-cols-2 gap-5">
      @foreach($settings as $group => $groupSettings)
      @php $gi = $groupIcons[$group] ?? ['icon'=>'fa-gear','color'=>'from-slate-400 to-slate-500','label'=>ucfirst($group)]; @endphp
      <div class="glass rounded-3xl p-6">
        <div class="flex items-center gap-3 mb-5">
          <div class="w-10 h-10 rounded-2xl bg-gradient-to-br {{ $gi['color'] }} flex items-center justify-center text-white text-sm shadow-md">
            <i class="fa-solid {{ $gi['icon'] }}"></i>
          </div>
          <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-sm font-outfit">{{ $gi['label'] }}</h3>
        </div>
        <div class="space-y-4">
          @foreach($groupSettings as $s)
          <div>
            <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">
              {{ $s->label }}
            </label>
            @if($s->type === 'boolean')
              <label class="relative inline-flex items-center cursor-pointer gap-3">
                <input type="hidden"   name="{{ $s->key }}" value="0">
                <input type="checkbox" name="{{ $s->key }}" value="1"
                  class="sr-only peer" {{ $s->value ? 'checked' : '' }}>
                <div class="relative w-11 h-6 bg-slate-200 dark:bg-slate-700 rounded-full peer-checked:bg-gradient-to-r peer-checked:from-purple-500 peer-checked:to-fuchsia-600 transition-all">
                  <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-all peer-checked:translate-x-5 peer peer-checked:left-5 duration-200"></div>
                </div>
                <span class="text-xs font-semibold text-slate-600 dark:text-slate-400">{{ $s->value ? 'Enabled' : 'Disabled' }}</span>
              </label>
            @elseif($s->type === 'textarea')
              <textarea name="{{ $s->key }}" rows="3" class="inp text-xs resize-none">{{ $s->value }}</textarea>
            @elseif($s->type === 'color')
              <div class="flex items-center gap-3">
                <input type="color" name="{{ $s->key }}" value="{{ $s->value ?? '#7c3aed' }}"
                  class="w-10 h-10 rounded-xl border border-slate-200 dark:border-white/10 cursor-pointer bg-transparent">
                <span class="text-xs font-mono text-slate-500 dark:text-slate-400">{{ $s->value }}</span>
              </div>
            @else
              <input type="text" name="{{ $s->key }}" value="{{ $s->value }}" class="inp text-xs">
            @endif
          </div>
          @endforeach
        </div>
      </div>
      @endforeach
    </div>

    <div class="flex justify-end gap-3 mt-2">
      <a href="{{ route('admin.dashboard') }}" class="btn-secondary px-6 py-3 text-xs">
        <i class="fa-solid fa-xmark"></i> Cancel
      </a>
      <button type="submit" class="btn-primary px-8 py-3 text-xs">
        <i class="fa-solid fa-floppy-disk"></i> Save Settings
      </button>
    </div>
  </form>

</div>
@endsection

@push('scripts')
<script>
// Live update toggle label text
document.querySelectorAll('input[type="checkbox"].sr-only').forEach(cb => {
  cb.addEventListener('change', function() {
    const label = this.closest('label').querySelector('span');
    if (label) label.textContent = this.checked ? 'Enabled' : 'Disabled';
  });
});
</script>
@endpush
