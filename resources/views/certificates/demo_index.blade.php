@extends('layouts.app')

@section('title', 'Demo Certificates Showcase')
@section('page-title', 'Demo Certificates')

@section('content')
<div class="space-y-6">

    {{-- Top Hero Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-800 p-6 lg:p-8 text-white shadow-xl">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-[11px] font-bold uppercase tracking-wider text-emerald-100">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    Official Certificate Samples
                </div>
                <h1 class="text-2xl lg:text-3xl font-extrabold font-outfit">Demo Certificates Showcase</h1>
                <p class="text-emerald-100/90 text-xs sm:text-sm max-w-xl">
                    Preview and download sample demo certificates across multiple learning domains. No course completion required to view sample formats!
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('courses.mine') }}"
                   class="px-4 py-2.5 rounded-2xl bg-white text-emerald-800 hover:bg-emerald-50 font-bold text-xs shadow-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-award text-emerald-600"></i> My Real Certificates
                </a>
            </div>
        </div>
    </div>

    {{-- Demo Certificates Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($demoCertificates as $key => $cert)
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden card-lift flex flex-col justify-between">
            <div>
                {{-- Decorative Header Banner --}}
                <div class="h-28 bg-gradient-to-br {{ $cert['gradient'] }} p-4 relative flex flex-col justify-between text-white">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded-full bg-white/20 backdrop-blur-md text-[10px] font-extrabold uppercase tracking-wider border border-white/30">
                            {{ $cert['category'] }}
                        </span>
                        <span class="w-8 h-8 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center text-sm">
                            <i class="fa-solid {{ $cert['icon'] }}"></i>
                        </span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-emerald-100 uppercase tracking-widest">Sample Certificate</span>
                        <h3 class="font-extrabold text-sm font-outfit truncate">{{ $cert['title'] }}</h3>
                    </div>
                </div>

                {{-- Body Details --}}
                <div class="p-5 space-y-4">
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 text-center space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Recipient Name Placeholder</span>
                        <p class="font-bold text-emerald-700 text-xs tracking-wider border-b border-dashed border-emerald-300 pb-1 inline-block">
                            [ YOUR NAME HERE / DEMO PARTICIPANT ]
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-[11px]">
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-[9px] font-bold text-slate-400 uppercase block">Level</span>
                            <span class="font-bold text-slate-800">{{ $cert['level'] }}</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-[9px] font-bold text-slate-400 uppercase block">Duration</span>
                            <span class="font-bold text-slate-800">{{ $cert['hours'] }} Hours</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Action Buttons --}}
            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center gap-2">
                <a href="{{ route('certificates.demo.view', $key) }}" target="_blank"
                   class="flex-1 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs text-center border border-slate-200 transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-eye text-slate-500"></i> Live Preview
                </a>
                <a href="{{ route('certificates.demo.download', $key) }}"
                   class="flex-1 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs text-center shadow-md transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-download"></i> Download PDF
                </a>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection
