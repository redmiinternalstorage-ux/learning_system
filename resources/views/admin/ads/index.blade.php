@extends('admin.layout')
@section('title', 'Ad Manager & Financial Tracking')
@section('page-title', 'Ad Manager & Financial Tracking')

@section('content')
<div class="space-y-6">

    {{-- Top Action & Metrics Bar --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Total Campaigns Run --}}
        <div class="glass p-5 rounded-3xl border border-slate-100 dark:border-white/5 flex items-center justify-between">
            <div>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Total Ads Run</p>
                <h3 class="text-2xl font-black text-slate-800 dark:text-slate-100 font-outfit mt-1">{{ $totalAdsRun }}</h3>
                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 mt-1 inline-block">
                    <i class="fa-solid fa-circle text-[6px] mr-1"></i> {{ $activeAdsCount }} Active Now
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center text-white shadow-lg shadow-rose-500/25">
                <i class="fa-solid fa-rectangle-ad text-lg"></i>
            </div>
        </div>

        {{-- Card 2: Total Collection --}}
        <div class="glass p-5 rounded-3xl border border-slate-100 dark:border-white/5 flex items-center justify-between">
            <div>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Total Collection</p>
                <h3 class="text-2xl font-black text-slate-800 dark:text-slate-100 font-outfit mt-1">₹{{ number_format($totalCollection, 2) }}</h3>
                <span class="text-[10px] font-bold text-emerald-500 mt-1 inline-block">Ad Revenue Total</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/25">
                <i class="fa-solid fa-indian-rupee-sign text-lg"></i>
            </div>
        </div>

        {{-- Card 3: Advance Received --}}
        <div class="glass p-5 rounded-3xl border border-slate-100 dark:border-white/5 flex items-center justify-between">
            <div>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Advance Received</p>
                <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 font-outfit mt-1">₹{{ number_format($totalAdvanceReceived, 2) }}</h3>
                <span class="text-[10px] font-bold text-slate-400 mt-1 inline-block">Advance Paid</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/25">
                <i class="fa-solid fa-money-bill-wave text-lg"></i>
            </div>
        </div>

        {{-- Card 4: Pending Dues --}}
        <div class="glass p-5 rounded-3xl border border-slate-100 dark:border-white/5 flex items-center justify-between">
            <div>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Remaining Balance</p>
                <h3 class="text-2xl font-black text-rose-600 dark:text-rose-400 font-outfit mt-1">₹{{ number_format($totalPendingBalance, 2) }}</h3>
                <span class="text-[10px] font-bold text-amber-500 mt-1 inline-block">Pending Dues</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-lg shadow-amber-500/25">
                <i class="fa-solid fa-clock-rotate-left text-lg"></i>
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="font-extrabold text-slate-800 dark:text-slate-100 text-base font-outfit">All Ad Campaigns</h2>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Track duration, collections &amp; payments</p>
        </div>
        <a href="{{ route('admin.ads.create') }}" class="btn-primary self-start sm:self-auto flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Create New Ad Campaign
        </a>
    </div>

    {{-- Campaigns Table --}}
    <div class="glass rounded-3xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="admin-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Media</th>
                        <th class="text-left">Campaign &amp; Description</th>
                        <th class="text-left">Sponsor</th>
                        <th class="text-left">Run Duration</th>
                        <th class="text-left">Financial Collection</th>
                        <th class="text-left">Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ads as $ad)
                    <tr>
                        {{-- Preview --}}
                        <td>
                            <div class="w-16 h-12 rounded-2xl overflow-hidden border border-slate-200 dark:border-white/8 bg-slate-100 dark:bg-white/5 flex items-center justify-center flex-shrink-0 shadow-xs">
                                @if($ad->type === 'image')
                                    <img src="{{ $ad->media_path }}" alt="Preview" class="w-full h-full object-cover">
                                @else
                                    <div class="flex flex-col items-center text-rose-500">
                                        <i class="fa-solid fa-circle-play text-base"></i>
                                        <span class="text-[8px] font-bold mt-0.5">Video</span>
                                    </div>
                                @endif
                            </div>
                        </td>

                        {{-- Campaign & Description --}}
                        <td class="max-w-xs">
                            <p class="font-bold text-slate-800 dark:text-slate-200 text-xs">{{ $ad->title }}</p>
                            @if($ad->description)
                                <p class="text-slate-400 text-[11px] truncate mt-0.5">{{ Str::limit($ad->description, 50) }}</p>
                            @endif
                            @if($ad->link_url)
                                <a href="{{ $ad->link_url }}" target="_blank"
                                   class="text-emerald-600 hover:underline text-[10px] mt-0.5 font-bold flex items-center gap-1">
                                    <i class="fa-solid fa-link text-[8px]"></i> Redirect Link
                                </a>
                            @endif
                        </td>

                        {{-- Sponsor --}}
                        <td>
                            <span class="badge badge-gray font-outfit uppercase">{{ $ad->powered_by }}</span>
                        </td>

                        {{-- Run Duration --}}
                        <td>
                            <div class="text-xs">
                                <span class="font-bold text-slate-800 dark:text-slate-200 block">{{ $ad->duration_days ?? 7 }} Days</span>
                                <span class="text-[10px] text-slate-400">
                                    @if($ad->start_date)
                                        {{ $ad->start_date->format('d M') }} — {{ $ad->deadline ? $ad->deadline->format('d M Y') : 'Ongoing' }}
                                    @else
                                        Deadline: {{ $ad->deadline ? $ad->deadline->format('d M Y') : 'Indefinite' }}
                                    @endif
                                </span>
                            </div>
                        </td>

                        {{-- Financial Collection --}}
                        <td>
                            <div class="text-xs space-y-0.5">
                                <p class="font-bold text-slate-900 dark:text-slate-100">Total: ₹{{ number_format($ad->total_amount, 2) }}</p>
                                <p class="text-[10px] text-emerald-600 font-bold">Advance: ₹{{ number_format($ad->advance_paid, 2) }}</p>
                                @if($ad->due_amount > 0)
                                    <p class="text-[10px] text-rose-500 font-extrabold">Due: ₹{{ number_format($ad->due_amount, 2) }}</p>
                                @else
                                    <span class="inline-block px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[9px] font-bold border border-emerald-200">Paid Full</span>
                                @endif
                            </div>
                        </td>

                        {{-- Status toggle --}}
                        <td>
                            <form method="POST" action="{{ route('admin.ads.toggle', $ad) }}">
                                @csrf
                                <button type="submit" class="transition">
                                    @if($ad->is_active)
                                        <span class="badge badge-green hover:opacity-80"><i class="fa-solid fa-circle text-[6px]"></i> Active</span>
                                    @else
                                        <span class="badge badge-gray hover:opacity-80">Inactive</span>
                                    @endif
                                </button>
                            </form>
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.ads.edit', $ad) }}"
                                   class="text-[11px] font-bold text-amber-600 dark:text-amber-400 hover:text-amber-800 transition flex items-center gap-1">
                                    <i class="fa-solid fa-pen text-[9px]"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('admin.ads.destroy', $ad) }}"
                                      onsubmit="return confirm('Delete campaign: {{ addslashes($ad->title) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="text-[11px] font-bold text-rose-500 hover:text-rose-700 transition flex items-center gap-1">
                                        <i class="fa-solid fa-trash text-[9px]"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-14 text-center">
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-300 dark:text-slate-600 text-2xl mx-auto mb-3">
                                <i class="fa-solid fa-rectangle-ad"></i>
                            </div>
                            <p class="font-bold text-slate-700 dark:text-slate-300 text-sm font-outfit">No ad campaigns yet</p>
                            <p class="text-slate-400 text-xs mt-1">
                                <a href="{{ route('admin.ads.create') }}" class="text-emerald-600 font-bold hover:underline">Create your first ad campaign</a>
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ads->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 dark:border-white/5 text-[11px] font-bold text-slate-400 bg-slate-50/30 dark:bg-white/[0.01]">
            {{ $ads->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
