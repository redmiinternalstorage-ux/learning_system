@extends('admin.layout')
@section('title','Add Staff Member')
@section('page-title','Add Staff Member')

@section('content')
<div class="max-w-xl space-y-5">

  <a href="{{ route('admin.staff.index') }}" class="btn-secondary text-xs px-4 py-2 inline-flex">
    <i class="fa-solid fa-arrow-left"></i> Back to Staff
  </a>

  <div class="glass rounded-2xl p-6">
    <div class="flex items-center gap-3 mb-6">
      <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-fuchsia-500 to-purple-600 flex items-center justify-center text-white shadow-lg shadow-fuchsia-500/30">
        <i class="fa-solid fa-user-plus text-lg"></i>
      </div>
      <div>
        <h2 class="font-extrabold text-slate-800 dark:text-slate-100 text-base font-outfit">Create Staff Account</h2>
        <p class="text-slate-400 text-xs mt-0.5">Staff can access admin panel · Finance manages their salary</p>
      </div>
    </div>

    <form method="POST" action="{{ route('admin.staff.store') }}" class="space-y-4">
      @csrf

      <div class="form-section pb-4 mb-4">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Account Details</p>
        <div class="grid sm:grid-cols-2 gap-4">
          <div class="sm:col-span-2">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Full Name <span class="text-rose-400">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required maxlength="255" placeholder="e.g. AK Patel" class="inp text-sm">
            @error('name')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Email <span class="text-rose-400">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}" required placeholder="staff@example.com" class="inp text-sm">
            @error('email')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Position</label>
            <input type="text" name="position" value="{{ old('position') }}" placeholder="e.g. Support Staff" class="inp text-sm">
          </div>
          <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Password <span class="text-rose-400">*</span></label>
            <input type="password" name="password" required minlength="8" placeholder="Min. 8 characters" class="inp text-sm">
            @error('password')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Confirm Password <span class="text-rose-400">*</span></label>
            <input type="password" name="password_confirmation" required minlength="8" placeholder="Re-enter password" class="inp text-sm">
          </div>
        </div>
      </div>

      <div>
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Salary Details</p>
        <div class="grid sm:grid-cols-3 gap-4">
          <div class="sm:col-span-2">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Monthly Salary</label>
            <input type="number" name="salary" value="{{ old('salary') }}" min="0" step="0.01" placeholder="e.g. 15000" class="inp text-sm">
            @error('salary')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Currency</label>
            <select name="salary_currency" class="inp text-sm">
              @foreach(['INR','USD','EUR','GBP','AED'] as $c)
              <option value="{{ $c }}" {{ old('salary_currency','INR')===$c?'selected':'' }}>{{ $c }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Pay Cycle</label>
            <select name="salary_cycle" class="inp text-sm">
              @foreach(['monthly'=>'Monthly','weekly'=>'Weekly','daily'=>'Daily','yearly'=>'Yearly'] as $k=>$v)
              <option value="{{ $k }}" {{ old('salary_cycle','monthly')===$k?'selected':'' }}>{{ $v }}</option>
              @endforeach
            </select>
          </div>
          <div class="sm:col-span-2">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Joining Date</label>
            <input type="date" name="joining_date" value="{{ old('joining_date',now()->toDateString()) }}" class="inp text-sm">
          </div>
        </div>
        <div class="mt-4">
          <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Notes</label>
          <textarea name="staff_notes" rows="2" placeholder="Any notes about this staff member…" class="inp text-sm resize-none">{{ old('staff_notes') }}</textarea>
        </div>
      </div>

      <div class="p-4 rounded-xl bg-fuchsia-50 dark:bg-fuchsia-950/20 border border-fuchsia-200 dark:border-fuchsia-900/30 text-xs text-fuchsia-700 dark:text-fuchsia-300">
        <i class="fa-solid fa-circle-info mr-1"></i>
        A welcome email will be sent. Staff login at <strong>/login</strong> using their email &amp; password.
      </div>

      <div class="flex gap-3 pt-1">
        <button type="submit" class="btn-primary text-xs">
          <i class="fa-solid fa-user-plus"></i> Create Staff Account
        </button>
        <a href="{{ route('admin.staff.index') }}" class="btn-secondary text-xs">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
