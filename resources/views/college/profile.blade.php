@extends('college.layout')
@section('title','College Profile')
@section('page-title','College Profile')

@section('content')
<div class="max-w-lg space-y-5">
    <div class="glass rounded-3xl p-6">
        <div class="flex items-center gap-4 pb-5 border-b border-slate-100 mb-5">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white flex items-center justify-center font-black text-2xl font-outfit">
                {{ strtoupper(substr($college->college_name,0,1)) }}
            </div>
            <div>
                <p class="font-extrabold text-slate-800 text-base">{{ $college->college_name }}</p>
                <p class="text-slate-400 text-xs">{{ $college->email }}</p>
                <span class="inline-block mt-1 text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full {{ $college->isActive() ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                    {{ $college->status }}
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('college.profile.update') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">College Name</label>
                <input type="text" name="college_name" value="{{ old('college_name', $college->college_name) }}" required class="inp text-xs">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Contact Person</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person', $college->contact_person) }}" class="inp text-xs" placeholder="Principal / Coordinator">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $college->phone) }}" class="inp text-xs">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Address</label>
                <input type="text" name="address" value="{{ old('address', $college->address) }}" class="inp text-xs">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Website</label>
                <input type="url" name="website" value="{{ old('website', $college->website) }}" class="inp text-xs" placeholder="https://...">
            </div>
            <div class="border-t border-slate-100 pt-4">
                <label class="block text-xs font-bold text-slate-600 mb-1.5">New Password <span class="text-slate-400 font-normal">(leave blank to keep)</span></label>
                <input type="password" name="password" class="inp text-xs" placeholder="Min 8 characters">
                <input type="password" name="password_confirmation" class="inp text-xs mt-2" placeholder="Confirm new password">
            </div>
            @if($errors->any())
            <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 text-xs">
                @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
            </div>
            @endif
            <button type="submit" class="btn-primary w-full text-xs">
                <i class="fa-solid fa-save"></i> Save Profile
            </button>
        </form>
    </div>
</div>
@endsection
