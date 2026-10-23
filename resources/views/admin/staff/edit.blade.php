@extends('admin.layout')
@section('title','Edit Staff — '.$staff->name)
@section('page-title','Edit Staff Member')

@section('content')
<div class="max-w-xl space-y-5">

  <a href="{{ route('admin.staff.index') }}" class="btn-secondary text-xs px-4 py-2 inline-flex">
    <i class="fa-solid fa-arrow-left"></i> Back to Staff
  </a>

  <div class="glass rounded-2xl p-6">
    <div class="flex items-center gap-3 mb-6">
      <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-fuchsia-400 to-purple-600 flex items-center justify-center text-white text-lg font-bold shadow-lg">
        {{ strtoupper(substr($staff->name,0,1)) }}
      </div>
      <div>
        <h2 class="font-extrabold text-slate-800 dark:text-slate-100 text-base font-outfit">{{ $staff->name }}</h2>
        <p class="text-slate-400 text-xs">{{ $staff->email }}</p>
      </div>
      <span class="ml-auto badge {{ $staff->is_blocked ? 'badge-rose' : 'badge-green' }}">
        {{ $staff->is_blocked ? 'Blocked' : 'Active' }}
      </span>
    </div>

    <form method="POST" action="{{ route('admin.staff.update', $staff) }}" class="space-y-4">
      @csrf @method('PUT')

      <div class="form-section pb-4 mb-4">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Account Details</p>
        <div class="grid sm:grid-cols-2 gap-4">
          <div class="sm:col-span-2">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Full Name <span class="text-rose-400">*</span></label>
            <input type="text" name="name" value="{{ old('name',$staff->name) }}" required class="inp text-sm">
            @error('name')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Email <span class="text-rose-400">*</span></label>
            <input type="email" name="email" value="{{ old('email',$staff->email) }}" required class="inp text-sm">
            @error('email')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Position</label>
            <input type="text" name="position" value="{{ old('position',$staff->position) }}" placeholder="e.g. Support Staff" class="inp text-sm">
          </div>
          <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">New Password <span class="text-slate-400 normal-case font-normal">(leave blank to keep)</span></label>
            <input type="password" name="password" minlength="8" placeholder="Min. 8 characters" class="inp text-sm">
            @error('password')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Joining Date</label>
            <input type="date" name="joining_date" value="{{ old('joining_date',$staff->joining_date?->toDateString()) }}" class="inp text-sm">
          </div>
        </div>
      </div>

      <div>
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Salary Details</p>
        <div class="grid sm:grid-cols-3 gap-4">
          <div class="sm:col-span-2">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Salary Amount</label>
            <input type="number" name="salary" value="{{ old('salary',$staff->salary) }}" min="0" step="0.01" placeholder="e.g. 15000" class="inp text-sm">
          </div>
          <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Currency</label>
            <select name="salary_currency" class="inp text-sm">
              @foreach(['INR','USD','EUR','GBP','AED'] as $c)
              <option value="{{ $c }}" {{ old('salary_currency',$staff->salary_currency??'INR')===$c?'selected':'' }}>{{ $c }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Pay Cycle</label>
            <select name="salary_cycle" class="inp text-sm">
              @foreach(['monthly'=>'Monthly','weekly'=>'Weekly','daily'=>'Daily','yearly'=>'Yearly'] as $k=>$v)
              <option value="{{ $k }}" {{ old('salary_cycle',$staff->salary_cycle??'monthly')===$k?'selected':'' }}>{{ $v }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="mt-4">
          <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Notes</label>
          <textarea name="staff_notes" rows="2" placeholder="Any notes…" class="inp text-sm resize-none">{{ old('staff_notes',$staff->staff_notes) }}</textarea>
        </div>
      </div>

      <div class="flex gap-3 pt-1">
        <button type="submit" class="btn-primary text-xs">
          <i class="fa-solid fa-floppy-disk"></i> Save Changes
        </button>
        <a href="{{ route('admin.staff.show', $staff) }}" class="btn-secondary text-xs">Cancel</a>
      </div>
    </form>
  </div>

  {{-- Danger zone --}}
  <div class="glass rounded-2xl p-4 flex items-center gap-3 flex-wrap">
    <form method="POST" action="{{ route('admin.staff.toggle-block', $staff) }}">
      @csrf
      <button type="submit" class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold border transition
          {{ $staff->is_blocked ? 'bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 border-emerald-200 dark:border-emerald-900/30' : 'bg-amber-50 dark:bg-amber-950/20 text-amber-600 border-amber-200 dark:border-amber-900/30' }}">
        <i class="fa-solid {{ $staff->is_blocked ? 'fa-lock-open' : 'fa-ban' }}"></i>
        {{ $staff->is_blocked ? 'Unblock' : 'Block' }} Account
      </button>
    </form>
    <form method="POST" action="{{ route('admin.staff.destroy', $staff) }}" class="ml-auto"
          onsubmit="return confirm('Permanently delete this staff account?')">
      @csrf @method('DELETE')
      <button type="submit" class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-400 text-xs font-bold border border-rose-200 dark:border-rose-900/30 hover:bg-rose-100 transition">
        <i class="fa-solid fa-trash"></i> Delete Account
      </button>
    </form>
  </div>

</div>
@endsection
