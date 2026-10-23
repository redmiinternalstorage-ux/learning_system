<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Courses - Learning Curve</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/css/interactions.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        outfit: ['"Outfit"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-outfit { font-family: 'Outfit', sans-serif; }
        .btn-gradient {
            background: linear-gradient(135deg, #059669, #0d9488);
        }
        .btn-gradient:hover {
            background: linear-gradient(135deg, #047857, #0f766e);
        }
        .card-premium {
            background: #ffffff;
            border: 1px solid rgba(241, 245, 249, 0.8);
            box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.08), 0 2px 8px -1px rgba(148, 163, 184, 0.04);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-premium:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(148, 163, 184, 0.15), 0 10px 10px -5px rgba(148, 163, 184, 0.04);
            border-color: rgba(5, 150, 105, 0.25);
        }
    </style>
</head>
<body class="bg-[#f8fffe] text-slate-800">

{{-- Navbar --}}
<nav class="bg-white border-b border-slate-200/80 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('explore') }}" class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-600 flex items-center justify-center shadow-md shadow-emerald-500/20">
                    <i class="fa-solid fa-graduation-cap text-white text-sm"></i>
                </div>
                <span class="font-extrabold text-gray-900 text-lg font-outfit">Learning Curve</span>
            </a>
            <div class="flex items-center gap-3 text-xs font-bold uppercase tracking-wider">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2.5 rounded-xl bg-indigo-650 btn-gradient text-white shadow-xs transition"><i class="fa-solid fa-gauge mr-1.5"></i>Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 transition"><i class="fa-solid fa-right-to-bracket mr-1.5"></i>Login</a>
                    <a href="{{ route('register') }}" class="px-4 py-2.5 rounded-xl btn-gradient text-white shadow-xs transition"><i class="fa-solid fa-user-plus mr-1.5"></i>Register</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-extrabold text-gray-955 mb-2 font-outfit tracking-tight">All Courses</h1>
        <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">Explore our full course catalog and start learning today.</p>
    </div>

    <!-- Search & Filters -->
    <form method="GET" action="{{ route('courses.public') }}" class="flex flex-wrap gap-3 mb-8">
        <div class="relative flex-1 min-w-56">
            <i class="fa-solid fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search courses..."
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all">
        </div>
        <select name="category" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ request('category')==$cat ? 'selected':'' }}>{{ $cat }}</option>
            @endforeach
        </select>
        <select name="level" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all">
            <option value="">All Levels</option>
            @foreach(['Beginner','Intermediate','Advanced'] as $lv)
                <option value="{{ $lv }}" {{ request('level')==$lv ? 'selected':'' }}>{{ $lv }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-5 py-2.5 rounded-xl btn-gradient text-white text-xs font-bold uppercase tracking-wider shadow-xs transition">
            Search
        </button>
        @if(request()->hasAny(['search','category','level']))
            <a href="{{ route('courses.public') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 text-xs font-bold uppercase tracking-wider transition">Clear</a>
        @endif
    </form>

    <!-- Course Grid -->
    @if($courses->isEmpty())
        <div class="text-center py-20 text-slate-400">
            <i class="fa-solid fa-magnifying-glass text-4xl mb-4 block text-slate-300"></i>
            No courses found. Try a different search term or category.
        </div>
    @else
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @php
        $catColors = [
            'Web Development'    => 'from-emerald-500 via-teal-500 to-emerald-600',
            'Data Science'       => 'from-teal-500 via-cyan-500 to-teal-600',
            'Design'             => 'from-pink-500 to-rose-600',
            'AI & ML'            => 'from-purple-500 to-violet-600',
            'Mobile Development' => 'from-orange-500 to-amber-600',
            'Marketing'          => 'from-yellow-500 to-orange-500',
        ];
        @endphp
        @foreach($courses as $course)
        <a href="{{ route('courses.show', $course) }}"
           class="bg-white rounded-2xl overflow-hidden group flex flex-col card-premium">
            <div class="h-40 relative">
                <div class="absolute inset-0 bg-gradient-to-br {{ $catColors[$course->category] ?? 'from-emerald-500 via-teal-500 to-emerald-600' }}"></div>
                <div class="relative z-10 h-full flex items-center justify-center">
                    <i class="fa-solid fa-circle-play text-white/40 group-hover:text-white/60 text-5xl transition-colors duration-200"></i>
                </div>
                <span class="absolute top-3 left-3 bg-white/90 text-emerald-900 text-[10px] font-bold uppercase tracking-wide px-2.5 py-1 rounded-lg">{{ $course->category }}</span>
                @if($course->price == 0)
                    <span class="absolute top-3 right-3 bg-emerald-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-lg">FREE</span>
                @endif
            </div>
            <div class="p-5 flex flex-col flex-1">
                <h3 class="font-bold text-slate-800 text-sm leading-snug mb-2 group-hover:text-emerald-600 transition line-clamp-2">{{ $course->title }}</h3>
                <p class="text-slate-400 text-[11px] leading-relaxed mb-3 line-clamp-2 flex-1">{{ $course->short_description ?? $course->description }}</p>
                <p class="text-slate-400 text-[10px] font-semibold mb-3"><i class="fa-solid fa-user-tie mr-1.5 text-emerald-500"></i>{{ $course->instructor_name }}</p>
                <div class="flex items-center justify-between text-[11px] text-slate-400 border-t border-slate-100 pt-3 mt-auto font-medium">
                    <div class="flex items-center gap-3">
                        <span><i class="fa-solid fa-signal mr-1 text-slate-400"></i>{{ $course->level }}</span>
                        <span><i class="fa-solid fa-clock mr-1 text-slate-400"></i>{{ $course->duration_hours }}h</span>
                    </div>
                    <span class="font-extrabold {{ $course->price == 0 ? 'text-emerald-600' : 'text-slate-800' }} text-sm">{{ $course->price_display }}</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($courses->hasPages())
    <div class="mt-8 flex justify-center gap-2 text-xs font-bold uppercase">
        @if($courses->onFirstPage())
            <span class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-300">← Prev</span>
        @else
            <a href="{{ $courses->previousPageUrl() }}" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition">← Prev</a>
        @endif
        <span class="px-4 py-2.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100 font-extrabold">{{ $courses->currentPage() }} / {{ $courses->lastPage() }}</span>
        @if($courses->hasMorePages())
            <a href="{{ $courses->nextPageUrl() }}" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition">Next →</a>
        @else
            <span class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-300">Next →</span>
        @endif
    </div>
    @endif
    @endif
</div>

<footer class="bg-white text-slate-400 py-10 mt-20 border-t border-emerald-100">
    <div class="max-w-7xl mx-auto px-4 text-center text-xs font-medium uppercase tracking-wider">
        © {{ date('Y') }} Learning Curve. All rights reserved.
    </div>
</footer>
@include('partials.ad-overlay')
@auth
@include('partials.ai-chatbot')
@endauth
@include('partials.global-loader')
</body>
</html>
