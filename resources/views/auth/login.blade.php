<x-guest-layout>
    <!-- Brand Header inside card -->
    <div class="text-center mb-6">
        <h1 class="text-3xl font-black tracking-widest text-white uppercase">INDOFROZ</h1>
        <p class="text-xs text-slate-400 mt-1 font-semibold tracking-widest uppercase">Welcome Back</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <!-- Error Alert Banner -->
    @if ($errors->any())
        <div class="mb-5 p-4 rounded-xl bg-red-950/80 border border-red-500/50 text-red-200 text-sm shadow-lg flex items-start space-x-3">
            <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="flex-1">
                <h3 class="font-bold text-red-400 text-sm">Gagal Masuk</h3>
                <ul class="mt-1 text-xs text-red-300 space-y-1 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" autocomplete="off" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block font-semibold text-xs text-slate-300 uppercase tracking-wider mb-2">Email Address <span class="text-rose-500 font-bold ml-1">*</span></label>
            <div class="relative">
                <div style="position: absolute; left: 0.875rem; top: 0; bottom: 0; display: flex; items-center: center; pointer-events: none;" class="flex items-center {{ $errors->has('email') ? 'text-red-400' : 'text-slate-500' }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                    </svg>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="off" 
                    style="padding-left: 2.75rem !important; padding-right: 2.75rem !important;"
                    class="block w-full py-3 bg-slate-950 border {{ $errors->has('email') ? 'border-red-500 focus:border-red-400 focus:ring-red-400' : 'border-slate-800 focus:border-indigo-500 focus:ring-indigo-500' }} focus:ring-2 text-white placeholder-slate-500 rounded-xl text-sm transition-all duration-150" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-400 text-xs font-medium" />
        </div>

        <!-- Password -->
        <div>
            <div class="mb-2">
                <label for="password" class="block font-semibold text-xs text-slate-300 uppercase tracking-wider">Password <span class="text-rose-500 font-bold ml-1">*</span></label>
            </div>
            <div class="relative">
                <div style="position: absolute; left: 0.875rem; top: 0; bottom: 0; display: flex; items-center: center; pointer-events: none;" class="flex items-center {{ $errors->has('password') ? 'text-red-400' : 'text-slate-500' }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    style="padding-left: 2.75rem !important; padding-right: 2.75rem !important;"
                    class="block w-full py-3 bg-slate-950 border {{ $errors->has('password') ? 'border-red-500 focus:border-red-400 focus:ring-red-400' : 'border-slate-800 focus:border-indigo-500 focus:ring-indigo-500' }} focus:ring-2 text-white placeholder-slate-500 rounded-xl text-sm transition-all duration-150" />
                <button type="button" id="toggle-password" 
                    style="position: absolute; right: 0.875rem; top: 0; bottom: 0; display: flex; align-items: center;"
                    class="text-slate-500 hover:text-slate-300 transition-colors focus:outline-none cursor-pointer">
                    <svg id="eye-icon" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg id="eye-off-icon" class="h-4 w-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-400 text-xs font-medium" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full flex justify-center items-center px-4 py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold rounded-xl shadow-md transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                Log In
            </button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const toggleButton = document.getElementById('toggle-password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeOffIcon = document.getElementById('eye-off-icon');

            // Reset browser autofilled credentials on initial load unless validation failed or old input exists
            @if(!$errors->any() && !old('email'))
            setTimeout(function() {
                if (emailInput) emailInput.value = '';
                if (passwordInput) passwordInput.value = '';
            }, 100);
            @endif

            if (toggleButton && passwordInput) {
                toggleButton.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    if (type === 'password') {
                        eyeIcon.classList.remove('hidden');
                        eyeOffIcon.classList.add('hidden');
                    } else {
                        eyeIcon.classList.add('hidden');
                        eyeOffIcon.classList.remove('hidden');
                    }
                });
            }
        });
    </script>
</x-guest-layout>
