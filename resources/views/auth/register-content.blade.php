<div class="max-w-md mx-auto w-full">
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-emerald-100">
        <div class="text-center space-y-1 mb-5">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center mx-auto mb-3 shadow-md shadow-emerald-500/20">
                <i class="fa-solid fa-user-plus text-white text-xl"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight font-outfit">Create your account</h1>
            <p class="text-slate-500 text-xs font-medium">Start learning in less than two minutes</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-3.5">
            @csrf
            <input type="hidden" name="role" value="student">

            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 mb-1.5">Full Name</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                        <i class="fa-regular fa-user"></i>
                    </div>
                    <input type="text" name="name" id="name" required
                           value="{{ old('name') }}"
                           placeholder="Your full name" autocomplete="name"
                           class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                </div>
            </div>

            <div>
                <label for="reg_email" class="block text-xs font-bold text-slate-700 mb-1.5">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                        <i class="fa-regular fa-envelope"></i>
                    </div>
                    <input type="email" name="email" id="reg_email" required
                           value="{{ old('email') }}"
                           placeholder="you@example.com" autocomplete="email"
                           class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                </div>
            </div>

            <div>
                <label for="reg_password" class="block text-xs font-bold text-slate-700 mb-1.5">Create Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <input type="password" name="password" id="reg_password" required
                           placeholder="Minimum 8 characters" autocomplete="new-password"
                           oninput="checkPasswordStrength(this.value)"
                           class="block w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                    <button type="button" onclick="togglePassword('reg_password', 'regEyeIcon')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                        <i class="fa-regular fa-eye" id="regEyeIcon"></i>
                    </button>
                </div>
                <div class="mt-2 space-y-1">
                    <div class="flex items-center justify-between text-[10px] font-bold">
                        <span class="text-slate-400">Strength:</span>
                        <span id="strengthLabel" class="text-slate-500">Too weak</span>
                    </div>
                    <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                        <div id="strengthBar" class="h-full w-0 bg-red-400 transition-all duration-300 rounded-full"></div>
                    </div>
                </div>
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1.5">Confirm Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           placeholder="Re-enter your password" autocomplete="new-password"
                           class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                </div>
            </div>

            <label class="flex items-start gap-2.5 cursor-pointer">
                <input type="checkbox" required name="terms" class="mt-0.5 w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500 flex-shrink-0">
                <span class="text-xs text-slate-600 font-medium leading-relaxed">I agree to the Terms of Service and Privacy Policy</span>
            </label>

            <button type="submit"
                    class="w-full py-3 px-4 rounded-xl text-white font-bold text-sm btn-gradient-primary shadow-lg shadow-emerald-500/25 flex items-center justify-center gap-2 group">
                <span>Create Account</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform text-xs"></i>
            </button>
        </form>

        <p class="text-center text-xs text-slate-500 font-medium mt-5">
            Already have an account?
            <button type="button" onclick="switchAuthTab('login')" class="font-extrabold text-emerald-600 hover:text-emerald-700 underline underline-offset-4 ml-1 bg-transparent border-0 cursor-pointer">
                Sign In
            </button>
        </p>
    </div>
</div>
