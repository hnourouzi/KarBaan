<x-layouts.guest title="ورود | کاربان">
    <x-card>
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-stone-900">ورود به کاربان</h1>
            <p class="mt-1 text-sm text-stone-500">برنامه روزانه و ثبت ساعات کار</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <x-input name="email" type="email" label="ایمیل" required autocomplete="username" />
            <x-input name="password" type="password" label="رمز عبور" required autocomplete="current-password" />

            <label class="flex items-center gap-2 text-sm text-stone-600">
                <input type="checkbox" name="remember" value="1" class="rounded border-stone-300">
                مرا به خاطر بسپار
            </label>

            <x-button class="w-full">ورود</x-button>
        </form>
    </x-card>
</x-layouts.guest>
