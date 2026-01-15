<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Kami telah mengirimkan kode OTP ke email Anda. Silakan masukkan kode tersebut di bawah ini.') }}
    </div>

    <form method="POST" action="{{ route('otp.verify.store') }}">
        @csrf

        <div>
            <x-input-label for="otp" :value="__('Kode OTP')" />
            <x-text-input id="otp" class="block mt-1 w-full text-center text-2xl tracking-widest" type="text" name="otp" required autofocus />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Verifikasi Masuk') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>