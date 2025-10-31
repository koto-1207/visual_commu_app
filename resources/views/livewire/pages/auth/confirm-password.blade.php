<?php

use Illuminate\Support\Facades\Auth;

use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

layout('components.layouts.app');

state(['password' => '']);

rules(['password' => ['required', 'string']]);

$confirmPassword = function () {
    $this->validate();

    if (
        !Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $this->password,
        ])
    ) {
        $this->addError('password', __('auth.password'));
        return;
    }

    session(['auth.password_confirmed_at' => time()]);

    $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
};

?>

<div class="min-h-screen flex items-center justify-center bg-pic-bg px-4">

    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-2xl shadow-lg">

        {{-- ロゴ --}}
        <div class="flex justify-center">
            <img src="{{ asset('images/logo.jpg') }}" alt="PicCommu Logo" class="h-20 w-auto">
        </div>

        {{-- タイトル --}}
        <h2 class="text-center text-3xl font-bold text-gray-700">
            パスワード確認
        </h2>

        {{-- 説明文 --}}
        <div class="text-sm text-gray-600 text-center">
            これは安全な領域です。続行する前に、パスワードを確認してください。
        </div>

        {{-- フォーム --}}
        <form wire:submit="confirmPassword" class="space-y-6">

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">パスワード</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required autofocus
                    wire:model="password"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-pic-mint focus:border-pic-mint sm:text-sm">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-lg font-medium text-white bg-pic-pink hover:bg-opacity-80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pic-pink transition duration-150">
                    確認
                </button>
            </div>
        </form>

    </div>
</div>
