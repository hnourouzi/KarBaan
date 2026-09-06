<x-layouts.guest title="ورود | کاربان">
    <x-card>
        <div class="mb-6 text-center">
            <h1 class="text-xl font-bold text-slate-900">ورود به کاربان</h1>
            <p class="mt-1 text-sm text-slate-500">برنامه روزانه و ثبت ساعات کار</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <x-input name="email" type="email" label="ایمیل" required autocomplete="username" />
            <x-input name="password" type="password" label="رمز عبور" required autocomplete="current-password" />

            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/20">
                مرا به خاطر بسپار
            </label>

            <x-button class="w-full">ورود</x-button>
        </form>
    </x-card>
</x-layouts.guest>
