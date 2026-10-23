@extends('layouts.app')
@section('title', 'Account Settings')
@section('page-title', 'Settings & Profile')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-extrabold text-2xl flex-shrink-0 shadow-md shadow-emerald-500/20">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div>
            <div class="flex items-center gap-2 flex-wrap">
                <h1 class="text-xl font-black text-slate-900 font-outfit">Account Settings</h1>
                <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">{{ ucfirst($user->role) }}</span>
            </div>
            <p class="text-slate-500 text-xs mt-1">Manage your profile, password, security, and data settings.</p>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm flex-shrink-0"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2">
        <i class="fa-solid fa-circle-exclamation text-rose-600 text-sm flex-shrink-0"></i> {{ session('error') }}
    </div>
    @endif
    @if($errors->any())
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium space-y-1">
        @foreach($errors->all() as $error)
        <div class="flex items-center gap-2"><i class="fa-solid fa-triangle-exclamation text-rose-500"></i> {{ $error }}</div>
        @endforeach
    </div>
    @endif

    {{-- ── Profile & Password ── --}}
    <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf

        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-user-gear text-emerald-600"></i>
                <h2 class="text-sm font-bold text-slate-800 font-outfit uppercase tracking-wider">Profile Details</h2>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Full Name</label>
                <div class="relative">
                    <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="inp pl-11" placeholder="Your full name">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Email Address</label>
                <div class="relative">
                    <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="inp pl-11" placeholder="your@email.com">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-key text-emerald-600"></i>
                    <h2 class="text-sm font-bold text-slate-800 font-outfit uppercase tracking-wider">Change Password</h2>
                </div>
                <span class="text-[10px] text-slate-400">(Leave blank to keep unchanged)</span>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">New Password</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="password" name="password" id="pwd_now" class="inp pl-11 pr-11" placeholder="Minimum 8 characters">
                    <button type="button" onclick="toggleVis('pwd_now','eye_now')"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <i class="fa-regular fa-eye text-sm" id="eye_now"></i>
                    </button>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Confirm New Password</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="password" name="password_confirmation" class="inp pl-11" placeholder="Re-enter new password">
                </div>
            </div>
        </div>

        <button type="submit"
                class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-sm shadow-md shadow-emerald-500/20 transition">
            <i class="fa-solid fa-floppy-disk mr-1.5"></i> Save Profile & Password
        </button>
    </form>

    {{-- ── Date-Based Password System ── --}}
    <div class="bg-white rounded-3xl border border-emerald-200 shadow-sm p-6 space-y-5">
        <div class="flex items-center justify-between border-b border-emerald-100 pb-4 flex-wrap gap-2">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900 font-outfit">Date-Based Password System</h2>
                    <p class="text-xs text-slate-500">Set different passwords for specific days of the month (max 3).</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-[11px] font-bold">
                {{ $user->datePasswords->count() }} / 3 Entries
            </span>
        </div>

        <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-slate-700 text-xs flex items-start gap-3 leading-relaxed">
            <i class="fa-solid fa-lightbulb text-amber-500 text-sm mt-0.5 flex-shrink-0"></i>
            <p>Set a password for (e.g.) the <strong>1st</strong> or <strong>5th</strong> of the month — the system will accept that password on that day automatically.</p>
        </div>

        @if($user->datePasswords->isNotEmpty())
        <div class="space-y-3">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active Rules</p>
            @foreach($user->datePasswords as $dp)
            <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50 flex items-center justify-between gap-3 flex-wrap">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex flex-col items-center justify-center">
                        <span class="text-[8px] uppercase font-bold text-slate-400 leading-none">Day</span>
                        <span class="text-base font-black font-outfit text-emerald-700 leading-none mt-0.5">{{ $dp->day_of_month }}</span>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-800">Every {{ ordinal_day($dp->day_of_month) }} of the month</p>
                        <p class="text-[10px] text-slate-500">
                            {{ $dp->is_active ? '🟢 Active' : '⚪ Disabled' }} · Updated {{ $dp->updated_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('profile.date-password.toggle', $dp) }}">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-xl border text-xs font-bold transition
                            {{ $dp->is_active ? 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-100' }}">
                            {{ $dp->is_active ? 'Disable' : 'Enable' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('profile.date-password.delete', $dp) }}"
                          onsubmit="return confirm('Remove password for day {{ $dp->day_of_month }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-8 h-8 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        @if($user->datePasswords->count() < 3)
        <form method="POST" action="{{ route('profile.date-password.add') }}" class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 space-y-4">
            @csrf
            <p class="text-xs font-bold text-emerald-800 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fa-solid fa-circle-plus"></i> Add Entry ({{ 3 - $user->datePasswords->count() }} remaining)
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Day of Month</label>
                    <select name="day_of_month" required class="inp text-xs py-2.5">
                        <option value="">Select Day</option>
                        @for($d = 1; $d <= 31; $d++)
                        <option value="{{ $d }}">Day {{ $d }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Password</label>
                    <input type="password" name="date_password" required placeholder="Min 8 chars" class="inp text-xs py-2.5">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Confirm</label>
                    <input type="password" name="date_password_confirmation" required placeholder="Repeat" class="inp text-xs py-2.5">
                </div>
            </div>
            <button type="submit" class="w-full py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs transition">
                <i class="fa-solid fa-plus-circle mr-1"></i> Add Entry
            </button>
        </form>
        @else
        <div class="p-3 rounded-xl bg-slate-100 border border-slate-200 text-slate-500 text-xs text-center font-medium">
            <i class="fa-solid fa-lock text-slate-400 mr-1"></i> Maximum 3 entries reached. Delete one to add new.
        </div>
        @endif
    </div>

    {{-- ── Data Export ── --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-vault text-emerald-600"></i>
                <h2 class="text-sm font-bold text-slate-800 font-outfit uppercase tracking-wider">Data Backup & Export</h2>
            </div>
            <span class="text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full">
                <i class="fa-solid fa-shield-halved text-[9px] mr-1"></i> Privacy
            </span>
        </div>
        <p class="text-slate-500 text-xs leading-relaxed">
            Export your personal learning report — courses, grades, certificates, and account metadata.
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <a href="{{ route('profile.data.download') }}"
               class="flex items-center justify-center gap-2 py-3 px-4 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs border border-slate-200 transition">
                <i class="fa-solid fa-file-pdf text-rose-500 text-sm"></i> Download PDF
            </a>
            <form method="POST" action="{{ route('profile.data.email') }}" class="w-full">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition">
                    <i class="fa-solid fa-envelope-circle-check text-sm"></i> Send to Email
                </button>
            </form>
        </div>
    </div>

    {{-- ── Account Info ── --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Account Information</h3>
        <div class="space-y-2 text-xs">
            <div class="flex justify-between py-1.5 border-b border-slate-100">
                <span class="text-slate-500">Role</span>
                <span class="font-bold text-slate-800">{{ ucfirst($user->role) }}</span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-slate-100">
                <span class="text-slate-500">Registered</span>
                <span class="font-bold text-slate-800">{{ $user->created_at->format('d F Y') }}</span>
            </div>
            <div class="flex justify-between py-1.5">
                <span class="text-slate-500">Status</span>
                <span class="font-bold {{ $user->is_blocked ? 'text-rose-600' : 'text-emerald-600' }} flex items-center gap-1">
                    <i class="fa-solid {{ $user->is_blocked ? 'fa-ban' : 'fa-circle-check' }} text-[10px]"></i>
                    {{ $user->is_blocked ? 'Blocked' : 'Active' }}
                </span>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function toggleVis(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    input.type  = input.type === 'password' ? 'text' : 'password';
    icon.className = input.type === 'password' ? 'fa-regular fa-eye text-sm' : 'fa-regular fa-eye-slash text-sm';
}
</script>
@endpush
