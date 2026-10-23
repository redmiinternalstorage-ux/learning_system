<div class="max-w-md mx-auto w-full">
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-emerald-100">
        <div class="text-center space-y-1 mb-6">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center mx-auto mb-3 shadow-md shadow-emerald-500/20">
                <i class="fa-solid fa-right-to-bracket text-white text-xl"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight font-outfit">Welcome back</h1>
            <p class="text-slate-500 text-xs font-medium">Sign in to continue your learning journey</p>
        </div>

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="user_role" value="student">

            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                        <i class="fa-regular fa-envelope"></i>
                    </div>
                    <input type="email" name="email" id="email" required
                           value="{{ old('email') }}"
                           placeholder="you@example.com" autocomplete="username"
                           class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-bold text-slate-700">Password</label>
                    <a href="{{ route('password.forgot') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 transition-colors">Forgot password?</a>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <input type="password" name="password" id="password" required
                           placeholder="••••••••" autocomplete="current-password"
                           class="block w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                    <button type="button" onclick="togglePassword('password', 'eyeIcon')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                        <i class="fa-regular fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" class="w-3.5 h-3.5 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                <span class="text-xs font-semibold text-slate-600">Keep me logged in</span>
            </label>

            <button type="submit"
                    class="w-full py-3 px-4 rounded-xl text-white font-bold text-sm btn-gradient-primary shadow-md shadow-emerald-500/20 flex items-center justify-center gap-2 group">
                <span>Sign In</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform text-xs"></i>
            </button>
        </form>

        <p class="text-center text-xs text-slate-500 font-medium mt-5">
            Don't have an account?
            <button type="button" onclick="switchAuthTab('register')" class="font-extrabold text-emerald-600 hover:text-emerald-700 underline underline-offset-4 ml-1 bg-transparent border-0 cursor-pointer">
                Create Account
            </button>
        </p>

        <p class="text-center mt-4">
            <a href="{{ route('courses.public') }}" class="text-[11px] font-bold text-slate-400 hover:text-emerald-600 transition">
                Browse courses without signing in
            </a>
        </p>
    </div>
</div>
