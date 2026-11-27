@extends('admin.layout')
@section('title','Add College')
@section('page-title','Add College Account')

@section('content')
<div class="max-w-lg">
    <a href="{{ route('admin.colleges.index') }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-slate-700 text-xs font-bold mb-5 transition">
        <i class="fa-solid fa-arrow-left text-[10px]"></i> Back to Colleges
    </a>
    <div class="glass rounded-3xl p-7">
        <form method="POST" action="{{ route('admin.colleges.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">College Name *</label>
                <input type="text" name="college_name" value="{{ old('college_name') }}" required class="inp">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="inp">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Password *</label>
                    <input type="password" name="password" required class="inp" placeholder="Min 6 chars">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Contact Person</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person') }}" class="inp">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="inp">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Student Domain</label>
                    <input type="text" name="domain" value="{{ old('domain') }}" class="inp" placeholder="students.college.edu">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Status</label>
                    <select name="status" class="inp font-bold">
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Address</label>
                <input type="text" name="address" value="{{ old('address') }}" class="inp">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Website</label>
                <input type="url" name="website" value="{{ old('website') }}" class="inp" placeholder="https://...">
            </div>
            @if($errors->any())
            <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 text-xs">
                @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
            </div>
            @endif
            <div class="flex gap-2 pt-2">
                <button type="submit" class="btn-primary flex-1"><i class="fa-solid fa-save"></i> Create College Account</button>
                <a href="{{ route('admin.colleges.index') }}" class="btn-secondary px-5">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
