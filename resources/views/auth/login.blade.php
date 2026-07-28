<x-guest-layout>
    @php
        $eyebrowLabel = 'Masuk ke Sistem';
        $subLabel = 'Masukkan kredensial Anda untuk mengakses dashboard.';
    @endphp

    <div class="mb-8">
        <p class="font-mono-label text-xs tracking-widest uppercase mb-3" style="color: #C08A2E;">{{ $eyebrowLabel }}</p>
        <h2 class="font-display text-3xl" style="color: #16213A;">Selamat datang kembali</h2>
        <p class="text-sm text-gray-500 mt-2">{{ $subLabel }}</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@instansi.go.id" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1.5 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#16325C] shadow-sm focus:ring-[#C08A2E]" name="remember">
                <span class="ms-2 text-sm text-gray-500">{{ __('Ingat saya') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm hover:underline" style="color: #16325C;" href="{{ route('password.request') }}">
                    {{ __('Lupa kata sandi?') }}
                </a>
            @endif
        </div>

        <button type="submit"
                class="w-full py-2.5 rounded-md text-sm font-medium text-white transition-colors duration-150"
                style="background: #16325C;"
                onmouseover="this.style.background='#0F2340'" onmouseout="this.style.background='#16325C'">
            {{ __('Masuk') }}
        </button>
    </form>

    <p class="font-mono-label text-[11px] text-gray-400 mt-10 lg:hidden">
        &copy; {{ date('Y') }} UPT BKN Pangkalpinang
    </p>
</x-guest-layout>
